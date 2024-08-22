<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Yajra\Acl\Models\Permission;
use Yajra\Acl\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define modules and permissions
        $modules = [
            'Message',
            'Room',
            'RoomUser',
            'User',
        ];

        $specials = [
            'Assign Permission',
            'Assign Role',
            'Edit Admin Permissions',
            'Edit Admin Roles',
            'Edit User Permissions',
            'Edit User Roles',
            'Edit User Status',
            'Edit User Password',
            'Impersonate User',
            'List Admin Permissions',
            'List Admin Roles',
            'List Permission Users',
            'List Role Users',
            'List User Permissions',
            'List User Roles',
        ];

        $userPermissions = [
            'User Can not Login',
        ];

        $permissions = $this->generatePermissions($modules, $specials, $userPermissions);

        // Insert or update permissions
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Sync permissions with roles
        $this->syncRolePermissions('admin');
        $this->syncRolePermissions('user');
        $this->syncRolePermissions('guest');
    }

    /**
     * Generate permissions based on the modules and specials provided.
     */
    private function generatePermissions(array $modules, array $specials, array $userPermissions): array
    {
        $permissions = [];

        foreach ($modules as $module) {
            $permissions = array_merge($permissions, $this->getModulePermissions($module));
        }

        foreach ($specials as $special) {
            $permissions[] = [
                'name'       => $special,
                'slug'       => mb_strtolower(Str::slug($special)),
                'system'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($userPermissions as $userPermission) {
            $permissions[] = [
                'name'       => $userPermission,
                'slug'       => mb_strtolower(Str::slug($userPermission)),
                'system'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $permissions;
    }

    /**
     * Get permissions for a specific module.
     */
    private function getModulePermissions(string $module): array
    {
        $permissions = [];
        $modulePermissions = [
            'Create',
            'View',
            'Edit',
            'List',
            'Delete'
        ];

        foreach ($modulePermissions as $action) {
            $permissions[] = [
                'name'       => "{$action} {$module}",
                'slug'       => strtolower("{$action}-" . Str::slug($module)),
                'system'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $permissions;
    }

    /**
     * Sync permissions with the role.
     */
    private function syncRolePermissions(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if ($role) {
            if ($roleSlug === 'admin') {
                // Admin has all permissions
                $permissions = Permission::all();
            } elseif ($roleSlug === 'user') {
                // User has specific permissions
                $permissions = Permission::whereIn('slug', [
                    'create-message',
                    'view-message',
                    'edit-message',
                    'list-message',
                    'delete-message',
                    'view-room',
                    'list-room',
                    'create-roomuser',
                    'view-roomuser',
                    'edit-roomuser',
                    'list-roomuser',
                    'delete-roomuser',
                ])->get();
            } elseif ($roleSlug === 'guest') {
                // Guest has only 'Room' list permission
                $permissions = Permission::where('slug', 'list-room')->get();
            }

            $role->syncPermissions($permissions->pluck('id'));
        }
    }
}