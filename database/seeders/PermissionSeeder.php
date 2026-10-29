<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Services\RolePermissionService;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        $permissions = [
            ['name' => 'Manage Staffs', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Affiliates', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Events', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Consultations', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Free Consultations', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Courses', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Students', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Service', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Transaction', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Cms Settings', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Setting', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Leads', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Admissions', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Student Documents', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ];

        Permission::upsert($permissions, ['name', 'guard_name']);

        $permissionService = app(RolePermissionService::class);
        $modulePermissions = collect($permissionService->permissions())->map(function ($permission) use ($now) {
            return [
                'name' => $permission['name'],
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();
        Permission::upsert($modulePermissions, ['name', 'guard_name']);

        $legacyDefaults = [
            'staff' => ['Manage Students', 'Manage Service', 'Manage Consultations', 'Manage Free Consultations', 'Manage Leads', 'Manage Admissions'],
            'consultant' => ['Manage Leads', 'Manage Student Documents', 'Manage Admissions', 'Manage Consultations'],
            'instructor' => ['Manage Courses'],
            'finance' => ['Manage Transaction'],
            'cms_editor' => ['Manage Cms Settings', 'Manage Events'],
        ];

        foreach ($permissionService->systemRoles() as $roleDefinition) {
            $role = Role::firstOrCreate(
                ['name' => $permissionService->systemRoleName($roleDefinition['key']), 'guard_name' => 'web'],
                ['status' => STATUS_ACTIVE]
            );

            $needsInitialPermissions = $role->wasRecentlyCreated || $role->permissions()->count() === 0;

            if ($needsInitialPermissions && isset($legacyDefaults[$roleDefinition['key']])) {
                $permissionService->grantLegacyDefaults($role, $legacyDefaults[$roleDefinition['key']]);
            }

            if ($needsInitialPermissions && isset($roleDefinition['portal_module'])) {
                $role->givePermissionTo(array_map(
                    fn ($action) => $roleDefinition['portal_module'] . '.' . $action,
                    ['view', 'add', 'edit']
                ));
            }

            if (in_array($roleDefinition['key'], ['staff', 'consultant', 'instructor', 'finance', 'cms_editor'], true)) {
                $role->givePermissionTo(['dashboard.view', 'profile.view', 'profile.edit']);
            }

            if ($roleDefinition['key'] === 'staff') {
                $role->givePermissionTo('reports.view');
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
