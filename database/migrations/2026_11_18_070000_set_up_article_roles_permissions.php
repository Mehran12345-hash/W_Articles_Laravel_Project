<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $roles = ['admin', 'editor', 'user', 'subscriber'];
        $permissions = [
            'view articles', 'create articles', 'edit own articles',
            'edit any articles', 'delete articles', 'manage users', 'manage roles',
        ];
        foreach ($roles as $name) {
            DB::table('roles')->insertOrIgnore([
                'name' => $name, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        foreach ($permissions as $name) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $roleIds = DB::table('roles')->where('guard_name', 'web')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->where('guard_name', 'web')->pluck('id', 'name');
        $grants = [
            'admin' => $permissions,
            'editor' => ['view articles', 'create articles', 'edit own articles'],
            'user' => ['view articles'],
            'subscriber' => ['view articles'],
        ];
        foreach ($grants as $role => $names) {
            foreach ($names as $name) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'role_id' => $roleIds[$role], 'permission_id' => $permissionIds[$name],
                ]);
            }
        }

        // Move the previous `users.role` strings into Spatie's real role records.
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->select(['id', 'role'])->orderBy('id')->chunk(100, function ($users) use ($roleIds) {
                foreach ($users as $user) {
                    $role = isset($roleIds[$user->role]) ? $user->role : 'user';
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id' => $roleIds[$role],
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                }
            });
            // Keep the old values as history, but avoid conflict with HasRoles.
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role', 'legacy_role');
            });
        } elseif (Schema::hasColumn('users', 'legacy_role')) {
            // If this is run in a partially updated installation.
            DB::table('users')->select(['id', 'legacy_role'])->orderBy('id')->chunk(100, function ($users) use ($roleIds) {
                foreach ($users as $user) {
                    $role = isset($roleIds[$user->legacy_role]) ? $user->legacy_role : 'user';
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id' => $roleIds[$role], 'model_type' => 'App\\Models\\User',
                        'model_id' => $user->id,
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        // Do not remove users' roles or permissions on rollback.
        // These may already contain live admin changes.
    }
};
