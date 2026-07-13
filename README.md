# RBAC Study Group Feature — File Placement Guide

Copy each file into your actual Laravel project at the matching path. Two
files **replace** existing ones you pasted earlier (with bugs fixed);
everything else is new.

## New files — copy as-is

| File here | Goes to |
|---|---|
| `app/Http/Middleware/EnsureIsAdmin.php` | `app/Http/Middleware/EnsureIsAdmin.php` |
| `app/Http/Middleware/EnsureIsLecturer.php` | `app/Http/Middleware/EnsureIsLecturer.php` |
| `app/Http/Middleware/EnsureIsStudent.php` | `app/Http/Middleware/EnsureIsStudent.php` |
| `app/Http/Controllers/GroupController.php` | `app/Http/Controllers/GroupController.php` |
| `tests/Feature/GroupControllerTest.php` | `tests/Feature/GroupControllerTest.php` |

## Replace existing files (bugs fixed — see inline comments)

| File here | Replaces | What was fixed |
|---|---|---|
| `app/Models/Group.php` | your `Group.php` | `creator()` was `belongsToMany(User::class, 'created_by')` — wrong, since `created_by` is a column not a pivot table. Now `belongsTo(User::class, 'created_by')`. |
| `bootstrap/app.php` | your `bootstrap/app.php` | Added the `admin` / `lecturer` / `student` middleware aliases (was an empty closure). |
| `routes/web.php` | your `routes/web.php` | Added the 5 group routes inside the existing `auth` middleware group. All your original routes (Auth, Topics, Quizzes) are untouched. |
| `database/migrations/xxxx_xx_xx_create_groups_table.php` | your groups migration | `foreignID(...)` → `foreignId(...)` (Laravel has no `foreignID` method — this would throw `BadMethodCallException`). |
| `database/migrations/xxxx_xx_xx_create_group_members_table.php` | your group_members migration | `userCurrent()` → `useCurrent()` (typo — `userCurrent` doesn't exist as a column modifier). |

**Important:** rename the two migration files here to match the timestamp
prefix of your *original* migration files (the `xxxx_xx_xx_` filenames are
placeholders) — Laravel runs migrations in filename order, and `groups` must
run before `group_members` and `topics` since both have foreign keys into it.

## Not touched / already correct

- `app/Models/User.php` — no changes needed
- `app/Models/Topic.php` — no changes needed
- `database/migrations/..._create_topics_table.php` — no changes needed
- `app/Enums/RoleEnum.php` — no changes needed

## After copying files in

```bash
php artisan migrate:fresh          # rebuild schema with the fixed migrations
php artisan test --filter=GroupControllerTest
```

## Still needs your input

- **`database/factories/UserFactory.php`** — the test suite assumes
  `User::factory()->create(['role' => RoleEnum::X])` works. If you don't have
  a factory yet, share your `User` fillable/casts (already have them from
  earlier) and I'll write one.
- Decide if you want `StoreGroupRequest` extracted as a Form Request instead
  of inline `$request->validate()` in `GroupController@store` — currently
  inline since it's only two fields.
