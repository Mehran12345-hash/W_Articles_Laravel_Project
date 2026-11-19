<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $roles = [
            'super-admin', 'supervisor', 'admin', 'editor', 'user', 'subscriber',
        ];

        $permissions = [
            'view dashboard',
            'view articles',
            'create articles',
            'edit own articles',
            'edit any articles',
            'delete articles',
            'publish articles',
            'view users',
            'manage users',
            'assign roles',
            'manage roles',
            'manage permissions',
        ];

        foreach ($roles as $name) {
            DB::table('roles')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        foreach ($permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        $roleIds = DB::table('roles')->where('guard_name', 'web')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->where('guard_name', 'web')->pluck('id', 'name');

        // Reset only the built-in roles to a safe, documented starting checklist.
        // Custom roles and their assignments are left untouched.
        DB::table('role_has_permissions')
            ->whereIn('role_id', collect($roles)->map(fn ($name) => $roleIds[$name])->all())
            ->delete();

        $grants = [
            'super-admin' => $permissions,
            'supervisor' => [
                'view dashboard', 'view articles', 'create articles',
                'edit own articles', 'edit any articles', 'publish articles',
                'view users',
            ],
            'admin' => [
                'view dashboard', 'view articles', 'create articles',
                'edit own articles', 'edit any articles', 'delete articles',
                'publish articles', 'view users', 'manage users', 'assign roles',
            ],
            'editor' => [
                'view dashboard', 'view articles', 'create articles',
                'edit own articles', 'publish articles',
            ],
            'user' => ['view dashboard', 'view articles'],
            'subscriber' => ['view dashboard', 'view articles'],
        ];

        foreach ($grants as $roleName => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'role_id' => $roleIds[$roleName],
                    'permission_id' => $permissionIds[$permissionName],
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Intentionally non-destructive: role assignments may have been customized after setup.
    }
};
