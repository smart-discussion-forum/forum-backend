<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\Blacklist;
use App\Models\User;
use App\Models\Warning;
use App\Notifications\UserBlacklisted;
use App\Notifications\WarningIssued;
use Illuminate\Console\Command;

class CheckInactiveUsers extends Command
{
    protected $signature = 'moderation:check-inactive-users';

    protected $description = 'Warn (twice) then temporarily blacklist users who have not communicated for a long time.';

    public function handle(): int
    {
        $reinstated = $this->reinstateExpiredBlacklists();

        $firstWarningDays = (int) config('moderation.inactivity_first_warning_days');
        $secondWarningDays = (int) config('moderation.inactivity_second_warning_days');
        $blacklistAfterDays = (int) config('moderation.inactivity_blacklist_after_days');
        $blacklistDays = (int) config('moderation.blacklist_duration_days');

        $cutoff = now()->subDays($firstWarningDays);

        $candidates = User::query()
            ->where('role', RoleEnum::Student)
            ->where('status', '!=', StatusEnum::Blacklisted)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_active')->orWhere('last_active', '<=', $cutoff);
            })
            ->get();

        $warned = 0;
        $blacklisted = 0;

        foreach ($candidates as $user) {
            $result = $this->processUser($user, $secondWarningDays, $blacklistAfterDays, $blacklistDays);

            if ($result === 'warned') {
                $warned++;
            } elseif ($result === 'blacklisted') {
                $blacklisted++;
            }
        }

        $this->info("Checked {$candidates->count()} inactive user(s). Warned: {$warned}. Blacklisted: {$blacklisted}. Reinstated: {$reinstated}.");

        return self::SUCCESS;
    }

    /**
     * Lift expired temporary blacklists so the configured duration is enforced.
     */
    private function reinstateExpiredBlacklists(): int
    {
        $expiredUserIds = Blacklist::query()
            ->whereNotNull('Expires_at')
            ->where('Expires_at', '<=', now())
            ->pluck('User_id')
            ->unique();

        $count = 0;

        foreach ($expiredUserIds as $userId) {
            $hasActive = Blacklist::query()
                ->where('User_id', $userId)
                ->where(function ($query) {
                    $query->whereNull('Expires_at')
                        ->orWhere('Expires_at', '>', now());
                })
                ->exists();

            if ($hasActive) {
                continue;
            }

            $user = User::find($userId);
            if ($user && $user->status === StatusEnum::Blacklisted) {
                $user->status = StatusEnum::Active;
                $user->save();
                $count++;
            }
        }

        return $count;
    }

    private function processUser(
        User $user,
        int $secondWarningDays,
        int $blacklistAfterDays,
        int $blacklistDays
    ): ?string {
        // Only count auto-inactivity warnings issued since the user's last
        // known activity. Once last_active moves forward (they communicate),
        // older warnings stop counting for this streak automatically.
        $sinceActivity = $user->last_active ?? now()->subYears(10);

        $activeWarnings = Warning::forUser($user->id)
            ->autoInactivity()
            ->where('Issued_at', '>=', $sinceActivity)
            ->orderBy('Issued_at')
            ->get();

        $warningCount = $activeWarnings->count();

        // First warning: inactive for the configured first-warning period.
        if ($warningCount === 0) {
            $this->issueInactivityWarning($user, 1);
            return 'warned';
        }

        $lastWarning = $activeWarnings->last();
        $lastIssuedAt = $lastWarning->Issued_at;

        // Second warning: still inactive after the gap following the first warning.
        if ($warningCount === 1) {
            if ($lastIssuedAt->gt(now()->subDays($secondWarningDays))) {
                return null;
            }

            $this->issueInactivityWarning($user, 2);
            return 'warned';
        }

        // Blacklist: still inactive one configured day after the second warning.
        if ($lastIssuedAt->gt(now()->subDays($blacklistAfterDays))) {
            return null;
        }

        $this->blacklistForInactivity($user, $blacklistDays);

        return 'blacklisted';
    }

    private function issueInactivityWarning(User $user, int $number): void
    {
        $warning = Warning::create([
            'User_id' => $user->id,
            'Reason' => "Automatic inactivity warning #{$number}: no communication on the platform for an extended period.",
            'Issued_at' => now(),
            'Source' => Warning::SOURCE_AUTO_INACTIVITY,
        ]);

        $user->notify(new WarningIssued($warning));
    }

    private function blacklistForInactivity(User $user, int $blacklistDays): void
    {
        $blacklist = Blacklist::create([
            'User_id' => $user->id,
            'Reason' => "Automatically blacklisted for {$blacklistDays} day(s) after 2 inactivity warnings with no response.",
            'Blacklisted_at' => now(),
            'Expires_at' => now()->addDays($blacklistDays),
        ]);

        $user->status = StatusEnum::Blacklisted;
        $user->save();

        $user->notify(new UserBlacklisted($blacklist));
    }
}
