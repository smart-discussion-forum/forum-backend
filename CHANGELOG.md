# Merge notes: self-contained patch for forum-backend-Endpoints-AGABA

This is a version of the moderation/notifications/groups merge patch built
specifically to apply cleanly on top of the **AGABA** branch, since AGABA
turned out to be a different snapshot than the ISiko tree the first patch
was built against.

## Why a separate build was needed
Diffing AGABA against the ISiko tree: 132 files were byte-identical
(including `routes/web.php`, `routes/api.php`, `Group.php`, `User.php`,
`QuizController.php`, `Api/TopicController.php`, `Api/PostController.php`
— so the route/controller additions apply with zero conflicts either way).
Only 4 files differed, and AGABA had the **older, un-fixed** versions of
all four:

- `app/Models/Warning.php` — missing `SOURCE_MANUAL`/`SOURCE_AUTO_INACTIVITY`
  constants and the `forUser()`/`manual()`/`autoInactivity()` scopes that
  `WarningController` depends on.
- `app/Models/Blacklist.php` — missing `isActive()`, which
  `BlacklistController` depends on.
- `bootstrap/app.php` — empty `withMiddleware()` closure, no `admin` /
  `lecturer` / `student` / `not_blacklisted` aliases registered at all. Any
  route using `->middleware('lecturer')` or `->middleware('not_blacklisted')`
  would throw an unknown-alias error without this.
- `app/Http/Controllers/TopicController.php` — missing the (previously
  unused) `NewTopicPosted` import; not a functional issue by itself, since
  this patch's copy already includes both the import and the actual
  notification-dispatch call.

This patch includes the already-fixed versions of all four, so it's
self-contained — you don't need to also pull anything from the other
branch first.

## Everything else in this patch is identical to the first merge patch
- `GroupController`, `DashboardController`, `WarningController`,
  `BlacklistController`, `NotificationController`, `DirectMessageController`
  and their routes in `routes/web.php` / `routes/api.php` (`/groups/manage`
  is placed before the `/groups/{id}` wildcard so it isn't swallowed as a
  param).
- `not_blacklisted` applied to message, direct-message, topic, and post
  creation routes.
- `Schedule::command('moderation:check-inactive-users')->daily();` added to
  `routes/console.php`.
- `NewTopicPosted` actually fired in `TopicController::groupStore()`,
  `QuizPublished` actually fired in `QuizController::announce()`.
- The typo'd duplicate `..._create_paticipation_marks_table.php` migration
  is still excluded (AGABA doesn't have it either, so nothing to worry
  about there).

## Before merging
- If AGABA has since been updated independently, diff these 4 files again
  before overwriting — same caution as before: don't blindly clobber
  someone else's newer fix.
- Run `GroupControllerTest.php` after merging to confirm the `lecturer`
  middleware alias gates group creation the way the test expects.
