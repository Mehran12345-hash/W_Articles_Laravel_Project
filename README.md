# W Stories — Laravel Blog & Content Workspace

A Laravel 12 article management project with a public blog, private dashboard, image uploads, team management, and a proper role-based access-control system.

**Stack:** Laravel 12, PHP 8.2+, Blade, CSS, MySQL, Spatie Laravel Permission 6.

## What is included

- Public blog at `/` showing published articles only.
- Login and registration at `/login` and `/register`.
- Private dashboard at `/dashboard` with content stats, recent articles and shortcuts.
- Article CRUD at `/articles`, including cover image upload, draft/published status and permission checks.
- Team members at `/users` with safe role assignment.
- Separate **Roles** page at `/roles` for creating, renaming and deleting custom roles.
- Separate **Permissions** page at `/permissions` with grouped checkbox lists to assign permissions to a selected role.
- Responsive sidebar, modern dashboard cards, readable typography and responsive admin pages.
- Existing W logo at `public/images/brand-logo.png`.

## Roles and their default access

| Role | Default access |
|---|---|
| Super Admin | Full access to every module, role and permission. The role is protected from permission changes in the UI. |
| Supervisor | View dashboard and team, review articles, edit any article, create articles and publish. Cannot change roles or permissions. |
| Admin | Manage articles and team members; can assign Editor, User or Subscriber roles. Cannot promote accounts to Supervisor/Admin/Super Admin or change access rules by default. |
| Editor | Create and edit their own articles and publish them. Cannot delete or edit another author's articles by default. |
| User | View the dashboard and published articles only. |
| Subscriber | Read-only dashboard and published article access. |

The permission checklist can be customized by a Super Admin. The new access-control migration resets only the built-in roles to the documented starting permissions; custom roles and their assignments are preserved. The built-in roles are protected from deletion and renaming. Custom roles can be created and assigned permissions.

## Permission groups

- **Dashboard:** view dashboard.
- **Articles & publishing:** view articles, create articles, edit own articles, edit any articles, delete articles, publish articles.
- **Team & users:** view users, manage users, assign roles.
- **Roles & permissions:** manage roles, manage permissions.

A checked box means the selected role is allowed to perform that action. Unchecking it removes that access. The permission is checked on the server as well as reflected in the UI; hiding a button alone is not treated as security.

## Windows / XAMPP setup

1. Back up your existing Laravel folder and export your MySQL database from phpMyAdmin before replacing anything.
2. Extract the ZIP and open `W_Articles_Laravel_Project` in VS Code. This is the folder containing `artisan`.
3. Start MySQL in XAMPP. Create a database named `first_project` if it does not already exist.
4. For a fresh install, copy the environment example:

```powershell
Copy-Item .env.example .env
```

5. Edit `.env` and confirm your MySQL settings. For a default XAMPP installation:

```dotenv
APP_NAME="W Stories"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=first_project
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

6. Run in PowerShell:

```powershell
composer install
php artisan key:generate
php artisan migrate
php artisan permission:cache-reset
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

The project ZIP includes its current `vendor` folder, but `composer install` is recommended if Composer reports missing dependencies. No npm build is required for the included CSS. If you change frontend assets, use `npm install` and `npm run build`.

**Existing installation warning:** If you are connecting this project to an existing database, keep the original `.env` and `APP_KEY`. Do not overwrite the key or generate a new one for a database that already has encrypted data or active sessions. Update only the database values if necessary. Do not run `php artisan migrate:fresh` because it deletes all tables and data.

## Make your account Super Admin

Public registration always creates a normal `user` account. It never allows a visitor to choose a privileged role.

1. Register your account at `/register`, or use an existing account.
2. Open PowerShell in the project directory and run:

```powershell
php artisan tinker
```

3. Inside Tinker, use your actual registered email:

```php
$user = App\Models\User::where('email', 'YOUR_EMAIL@example.com')->firstOrFail();
$user->syncRoles(['super-admin']);
```

4. Verify:

```php
$user->getRoleNames();
```

5. Type `exit`, log out and log in again. Open `/roles` and `/permissions`.

Do not type PHP expressions directly at the PowerShell prompt; they must be entered after starting Tinker.

## Main URLs

| Page | URL |
|---|---|
| Public blog | `http://127.0.0.1:8000/` |
| Login | `http://127.0.0.1:8000/login` |
| Register | `http://127.0.0.1:8000/register` |
| Dashboard | `http://127.0.0.1:8000/dashboard` |
| Articles | `http://127.0.0.1:8000/articles` |
| Team members | `http://127.0.0.1:8000/users` |
| Roles | `http://127.0.0.1:8000/roles` |
| Permissions | `http://127.0.0.1:8000/permissions` |

For a short beginner explanation of each role and how the checklist works, read `ROLE_PERMISSION_GUIDE.md`.

## How to use the separate access pages

1. Sign in as Super Admin.
2. Open **Roles** to see built-in roles, create custom roles, rename custom roles or delete unused custom roles.
3. Open **Permissions**. Choose a role from the dropdown.
4. Tick or untick the checkbox permissions grouped by Dashboard, Articles, Team & Users, and Roles & Permissions.
5. Press **Save permissions**. The change applies to everyone with that role.
6. Open **Team members** to assign roles. Admins can assign Editor/User/Subscriber; only Super Admin can assign or change Supervisor/Admin/Super Admin accounts.

## Main files

| File | Purpose |
|---|---|
| `routes/web.php` | Public blog and private workspace routes |
| `app/Http/Controllers/RolePermissionController.php` | Role create, rename and delete |
| `app/Http/Controllers/PermissionController.php` | Separate permission checklist and saving |
| `app/Http/Controllers/UserRoleController.php` | Safe role assignment to users |
| `app/Policies/ArticlePolicy.php` | Server-side article authorization |
| `app/Providers/AppServiceProvider.php` | Super Admin full-access rule and Article policy registration |
| `database/migrations/2026_11_18_080000_expand_roles_and_permissions.php` | Adds roles and permission catalogue safely |
| `resources/views/roles/` | Role directory and rename page |
| `resources/views/permissions/` | Separate grouped checkbox permissions page |
| `resources/views/users/` | Team members and role assignment |
| `resources/views/dashboard.blade.php` | Updated dashboard |
| `public/css/access-control.css` | Responsive dashboard and access-control design |
| `public/images/brand-logo.png` | Supplied W logo |

## Common issues

- **`permission` Artisan namespace missing:** Run `composer install`, then `php artisan optimize:clear`. This project requires `spatie/laravel-permission:^6.25`.
- **No `super-admin` role:** Run `php artisan migrate`, then assign it using the Tinker instructions above.
- **403 on Roles or Permissions:** Sign in as a Super Admin. These are intentionally restricted by default.
- **403 on article create/edit:** The account needs `create articles` or the appropriate edit permission.
- **Published article not visible:** Check that status is `published`; draft articles are private.
- **Image not visible:** Run `php artisan storage:link`; check `storage/app/public/article-covers/` and the `cover_image` column.
- **MySQL connection refused:** Start MySQL in XAMPP and confirm the port in `.env` matches XAMPP's MySQL port.

## Verification status

PHP syntax checks, route registration and Blade compilation should be run after installation. A live MySQL integration test is not claimed here because this build environment does not have a running MySQL server or the PDO MySQL/SQLite drivers. Run `php artisan test` on your Windows/XAMPP environment after confirming the PHP extensions required by your test setup.
