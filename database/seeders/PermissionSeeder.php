<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['created_at' => $permission['created_at'], 'updated_at' => $permission['updated_at']]
            );
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
