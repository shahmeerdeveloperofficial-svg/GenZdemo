<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffRoleModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_modal_shows_active_permission_roles_and_inline_role_creator(): void
    {
        $this->seed(PermissionSeeder::class);

        $role = new Role();
        $role->name = 'Admissions Coordinator';
        $role->guard_name = 'web';
        $role->status = STATUS_ACTIVE;
        $role->save();

        $admin = User::factory()->make([
            'id' => 1,
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.staffs.index'))
            ->assertOk()
            ->assertSee('Admissions Coordinator')
            ->assertSee('Add custom role')
            ->assertSee('Create a permission role')
            ->assertSee('Manage Admissions');
    }

    public function test_admin_can_create_a_custom_role_from_the_staff_role_creator(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->make([
            'id' => 1,
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.staffs.roles.store'), [
                'name' => 'Admissions Coordinator',
                'status' => STATUS_ACTIVE,
                'permissions' => ['Manage Admissions', 'Manage Student Documents'],
            ])
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.role.name', 'Admissions Coordinator');

        $this->assertDatabaseHas('roles', [
            'name' => 'Admissions Coordinator',
            'guard_name' => 'web',
            'status' => STATUS_ACTIVE,
        ]);
    }
}
