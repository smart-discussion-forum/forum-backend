<?php

namespace App\Console\Commands;

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

    protected $description = 'Warn and temporarily blacklist users who have not communicated for a long time (SDD requirement #4).';

    public function handle(): int
    {
        $inactivityDays = (int) config('moderation.inactivity_warning_days');
        $complianceDays = (int) config('moderation.compliance_window_days');
        $blacklistDays = (int) config('moderation.blacklist_duration_days');

        $cutoff = now()->subDays($inactivityDays);

        $candidates = User::where('status', '!=', StatusEnum::Blacklisted)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_active')->orWhere('last_active', '<=', $cutoff);
            })
            ->get();

        foreach ($candidates as $user) {
            $this->processUser($user, $complianceDays, $blacklistDays);
        }

        $this->info("Checked {$candidates->count()} inactive user(s).");

        return self::SUCCESS;
    }

    private function processUser(User $user, int $complianceDays, int $blacklistDays): void
    {
        // Only count auto-inactivity warnings issued since the user's last
        // known activity. Once last_active moves forward (they come back),
        // older warnings stop counting for this streak automatically.
        $sinceActivity = $user->last_active ?? now()->subYears(10);

        $activeWarnings = Warning::forUser($user->id)
            ->autoInactivity()
            ->where('Issued_at', '>=', $sinceActivity)
            ->orderBy('Issued_at')
            ->get();

        $warningCount = $activeWarnings->count();

        if ($warningCount === 0) {
            $this->issueInactivityWarning($user);
            return;
        }

        $lastWarning = $activeWarnings->last();

        // Give the user the full compliance window after their most recent
        // warning before escalating to the next step.
        if ($lastWarning->Issued_at->gt(now()->subDays($complianceDays))) {
            return;
        }

        if ($warningCount === 1) {
            $this->issueInactivityWarning($user);
            return;
        }

        // 2+ auto-inactivity warnings, still inactive after the compliance
        // window expired: blacklist for the configured duration.
        $this->blacklistForInactivity($user, $blacklistDays);
    }

    private function issueInactivityWarning(User $user): void
    {
        $warning = Warning::create([
            'User_id' => $user->id,
            'Reason' => 'Inactivity: no communication on the platform for an extended period.',
            'Issued_at' => now(),
            'Source' => Warning::SOURCE_AUTO_INACTIVITY,
        ]);

        $user->notify(new WarningIssued($warning));
    }

    private function blacklistForInactivity(User $user, int $blacklistDays): void
    {
        $blacklist = Blacklist::create([
            'User_id' => $user->id,
            'Reason' => 'Automatically blacklisted for continued inactivity after 2 warnings and no response within the compliance window.',
            'Blacklisted_at' => now(),
            'Expires_at' => now()->addDays($blacklistDays),
        ]);

        $user->status = StatusEnum::Blacklisted;
        $user->save();

        $user->notify(new UserBlacklisted($blacklist));
    }
}
