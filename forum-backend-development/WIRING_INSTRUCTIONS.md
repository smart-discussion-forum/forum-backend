# Wiring instructions — Joel's moderation & notifications code

These files are self-contained and safe to drop straight into their matching
folders (`app/Models`, `app/Http/Controllers`, `app/Http/Middleware`,
`app/Notifications`, `database/migrations`). They don't overwrite anything.

Three shared files still need small manual edits — I didn't touch these
directly since they're actively edited by others and I don't want to
silently clobber anyone's changes.

## 1. `routes/api.php` — add inside the existing `auth:sanctum` group

```php
use App\Http\Controllers\WarningController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\NotificationController;

// ...inside Route::middleware('auth:sanctum')->group(function () { ... }):

Route::post('/warnings', [WarningController::class, 'issue']);
Route::get('/warnings', [WarningController::class, 'index']);

Route::get('/blacklist', [BlacklistController::class, 'index']);
Route::post('/blacklist/{blacklistId}/lift', [BlacklistController::class, 'lift']);

Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
```

## 2. `bootstrap/app.php` — register the new middleware alias

Add `'not_blacklisted' => \App\Http\Middleware\EnsureNotBlacklisted::class,`
to the existing `$middleware->alias([...])` array (the same one that already
registers `admin`, `lecturer`, `student`).

Then apply it to whichever existing routes create posts/messages/topics,
e.g.:

```php
Route::post('/messages/send', [MessageController::class, 'send'])
    ->middleware('not_blacklisted');
Route::post('/direct-messages/send', [DirectMessageController::class, 'send'])
    ->middleware('not_blacklisted');
```

I didn't add this directly to `MessageController`'s or `TopicController`'s
routes myself since those routes belong to Justine's and the Topic
features — flag it to them so the middleware actually gets attached where
it's needed.

## 3. Hooking up `NewTopicPosted` and `QuizPublished`

These notification classes are ready, but firing them needs one line added
inside the controllers that actually create topics/quizzes (not mine to
edit, to avoid stepping on that code):

**In `TopicController` (wherever a topic is successfully created):**
```php
use App\Notifications\NewTopicPosted;

// after $topic = Topic::create([...]):
foreach ($topic->group->members as $member) {
    $member->notify(new NewTopicPosted($topic));
}
```
(Adjust `$topic->group->members` to whatever relationship actually returns
group members — I saw `groups()` on `User` but didn't see the inverse
relation on `Group`, so double check that name.)

**In `QuizController::announce()` (once Pearl's `Quiz` model exists):**
```php
use App\Notifications\QuizPublished;

// after $quiz->update(['announced_at' => now(), 'status' => 'announced']):
foreach ($quiz->targetStudents() as $student) { // adjust to real relation
    $student->notify(new QuizPublished($quiz));
}
```

## 4. Schedule the inactivity check

`app/Console/Commands/CheckInactiveUsers.php` implements SDD requirement #4
(inactivity → 2 warnings → compliance window → **temporary** auto-blacklist).
It needs to run automatically — add this to `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('moderation:check-inactive-users')->daily();
```

(If your Laravel version schedules from `bootstrap/app.php` instead via
`->withSchedule(...)`, put the same line in there instead — check which
one your project already uses before adding a second, conflicting spot.)

You can also run it manually any time to test it:
```
php artisan moderation:check-inactive-users
```

## 5. Config values (`config/moderation.php`)

Four tunables, all overridable via `.env` so no one has to touch code to
change them:

| Key | Default | Meaning |
|---|---|---|
| `MODERATION_INACTIVITY_WARNING_DAYS` | 14 | Days inactive before 1st auto warning |
| `MODERATION_COMPLIANCE_WINDOW_DAYS` | 7 | Grace period after each auto warning |
| `MODERATION_BLACKLIST_DURATION_DAYS` | 30 | How long an inactivity auto-blacklist lasts |
| `MODERATION_MANUAL_WARNING_LIMIT` | 2 | Manual (Lecturer/Admin) warnings before instant blacklist |

## Manual vs. automatic warnings — how they're kept separate

The `warnings` table now has a `Source` column (`manual` or
`auto_inactivity`), added by
`2026_07_07_190000_add_source_to_warnings_table.php`. The manual
"Lecturer/Admin issues a warning for any reason" endpoint (kept as an
extra feature per your request) only ever counts and blacklists based on
**manual** warnings. The scheduled command only ever counts and
blacklists based on **auto_inactivity** warnings. Neither can trigger or
reset the other.

## ⚠️ Separate bug worth flagging to the team

`EnsureIsAdmin`, `EnsureIsLecturer`, and `EnsureIsStudent` middleware
(in `app/Http/Middleware/`) check `$user->role !== User::ROLE_ADMIN`, etc.
Those `ROLE_*` constants don't exist anywhere on the `User` model — it uses
`RoleEnum` casting instead. Any route using the `admin`/`lecturer`/`student`
middleware aliases will throw an "undefined constant" error at runtime.

My new controllers avoid this by checking `$user->role` against
`RoleEnum::Admin` / `RoleEnum::Lecturer` directly instead of using those
middleware aliases — but someone (Pearl, since access-control middleware
was her dependency) should fix the underlying middleware before other
routes that rely on it get exercised.

## Also worth noting

The `warnings` and `blacklist` migrations aren't in the zip you sent me —
I built these files against the schema from the `development` branch on
GitHub, so pull latest `development` before merging this branch to make
sure those two migrations are present, along with the new
`create_notifications_table` migration included here.
