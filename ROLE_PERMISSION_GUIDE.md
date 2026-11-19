# Roles & Permissions — Simple Guide

## The simple difference

- **Role** = the person's job in the software (Super Admin, Supervisor, Admin, Editor, User).
- **Permission** = an action the person is allowed to do (create an article, publish an article, manage users).

A user gets permissions through their assigned role. The permissions page uses real checkboxes and saves them to Spatie's permission tables.

## Built-in roles

### Super Admin
- Full access to the whole system.
- Can create/rename/delete custom roles.
- Can open the separate Roles and Permissions pages.
- Can change permissions for other roles.
- Can assign any role to another account.
- Cannot remove their own Super Admin role from the Team Members page.

### Supervisor
- Can open the dashboard and view articles.
- Can create articles, review/edit any article and publish articles.
- Can view the team member list.
- Cannot assign roles or change permissions by default.

### Admin
- Can manage articles, delete articles and publish content.
- Can view and manage team members.
- Can assign Editor, User and Subscriber roles.
- Cannot promote an account to Supervisor, Admin or Super Admin.
- Cannot change role definitions or permission checklists by default.

### Editor
- Can create and publish articles.
- Can edit their own articles.
- Cannot delete articles or edit another author's articles by default.

### User / Subscriber
- Can open the dashboard and view published articles.
- Cannot create, edit or delete articles by default.

## The three access pages

1. **Team Members** (`/users`): see accounts and assign roles. Admin can assign lower-level content roles. Super Admin can assign every role.
2. **Roles** (`/roles`): create custom roles, rename custom roles and delete unused custom roles. Built-in roles are protected.
3. **Permissions** (`/permissions`): choose a role and tick/untick checkboxes grouped by Dashboard, Articles, Team & Users, and Roles & Permissions. Press Save Permissions.

## Promote your own account to Super Admin

Run this in PowerShell from the folder containing `artisan`:

```powershell
php artisan tinker
```

Then run inside Tinker:

```php
$user = App\Models\User::where('email', 'YOUR_REGISTERED_EMAIL')->firstOrFail();
$user->syncRoles(['super-admin']);
$user->getRoleNames();
```

Then type `exit`, log out and log in again.

## Important safety notes

- Public registration always assigns the normal `user` role. Visitors cannot register as Admin or Super Admin.
- Super Admin is protected from being edited/deleted in the role UI and has full access through the authorization layer.
- Admin cannot promote themselves or another account to a higher privileged role.
- Article permissions are checked in controllers/policies on the server. Hiding a button is not the security mechanism.
- Run `php artisan migrate`; never use `php artisan migrate:fresh` on a database containing user or article data.
