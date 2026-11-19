# W Stories — Access Control Refresh

## Updated in this build

- Rebuilt the dashboard with clearer typography, larger readable text, responsive statistic cards, writing activity, quick actions and recent articles.
- Added **Super Admin**, **Supervisor**, **Admin**, **Editor**, **User** and retained **Subscriber** as built-in roles.
- Separated **Roles** (`/roles`) from **Permissions** (`/permissions`).
- Roles page now supports creating custom roles, renaming custom roles, displaying assigned member/permission counts, and safely deleting unused custom roles.
- Permissions page now has a role selector and real checkbox checklist grouped into Dashboard, Articles & Publishing, Team & Users, and Roles & Permissions.
- Added a Super Admin full-access rule and protected the Super Admin role from being deleted or edited in the checklist.
- Admins can assign only Editor, User and Subscriber roles; only Super Admin can promote or modify Supervisor/Admin/Super Admin and custom-role accounts.
- Supervisors can review articles and see the team list, but cannot change roles or permissions by default.
- Added a new additive migration to seed the expanded roles and permissions. It resets the built-in role permission defaults while preserving custom roles and user-to-role assignments.
- Kept the public blog, login/register, article CRUD, cover image uploads and supplied logo.
- Added `ROLE_PERMISSION_GUIDE.md` for simple setup and role explanations.

## Verification

- PHP source lint: passed.
- Blade compile + generated PHP lint: passed.
- Route registration: passed, including separate roles and permissions endpoints.
- Full database feature tests: not run in this build environment because PDO MySQL/SQLite and several PHPUnit-required PHP extensions are unavailable. Run tests against your local XAMPP/MySQL installation.
