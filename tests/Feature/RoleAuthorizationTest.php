<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_legacy_admin_remains_a_super_admin_without_role_pivot_rows(): void
    {
        $admin = User::factory()->make(['role' => USER_ROLE_ADMIN]);

        $this->assertTrue(Gate::forUser($admin)->allows('Manage Setting'));
    }

    public function test_staff_and_new_operational_roles_receive_only_their_configured_abilities(): void
    {
        $staff = User::factory()->make(['role' => USER_ROLE_STAFF]);
        $instructor = User::factory()->make(['role' => USER_ROLE_INSTRUCTOR]);
        $finance = User::factory()->make(['role' => USER_ROLE_FINANCE]);
        $editor = User::factory()->make(['role' => USER_ROLE_CMS_EDITOR]);

        $this->assertTrue(Gate::forUser($staff)->allows('Manage Students'));
        $this->assertFalse(Gate::forUser($staff)->allows('Manage Transaction'));
        $this->assertTrue(Gate::forUser($instructor)->allows('Manage Courses'));
        $this->assertFalse(Gate::forUser($instructor)->allows('Manage Setting'));
        $this->assertTrue(Gate::forUser($finance)->allows('Manage Transaction'));
        $this->assertFalse(Gate::forUser($finance)->allows('Manage Students'));
        $this->assertTrue(Gate::forUser($editor)->allows('Manage Cms Settings'));
        $this->assertFalse(Gate::forUser($editor)->allows('Manage Transaction'));
    }

    public function test_role_labels_are_backward_compatible_and_include_new_portal_roles(): void
    {
        $this->assertSame('Admin', getRoleName(USER_ROLE_ADMIN));
        $this->assertSame('Instructor', getRoleName(USER_ROLE_INSTRUCTOR));
        $this->assertSame('Finance', getRoleName(USER_ROLE_FINANCE));
        $this->assertSame('CMS Editor', getRoleName(USER_ROLE_CMS_EDITOR));
        $this->assertSame('Unknown', getRoleName(999));
    }

    public function test_explicit_spatie_role_permissions_override_legacy_staff_defaults(): void
    {
        $role = Role::create([
            'name' => 'authorization-test-' . Str::uuid(),
            'guard_name' => 'web',
            'status' => STATUS_ACTIVE,
        ]);
        $role->syncPermissions(['Manage Courses']);

        $staff = User::create([
            'first_name' => 'Permission',
            'last_name' => 'Test',
            'email' => 'permission-test-' . Str::uuid() . '@example.test',
            'password' => bcrypt(Str::random(24)),
            'role' => USER_ROLE_STAFF,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
        $staff->assignRole($role);

        $this->assertTrue(Gate::forUser($staff)->allows('Manage Courses'));
        $this->assertFalse(Gate::forUser($staff)->allows('Manage Students'));

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Courses &amp; Learning', false)
            ->assertDontSee('Manage Students')
            ->assertDontSee('Contact Us');

        $this->actingAs($staff)
            ->get(route('admin.dashboard.metrics'))
            ->assertOk()
            ->assertJsonPath('kpis.totalStudent', 0)
            ->assertJsonPath('kpis.newLeads', 0)
            ->assertJsonPath('coursePopularity.labels', []);

        $this->actingAs($staff)
            ->get(route('admin.analytics'))
            ->assertOk();

        $this->actingAs($staff)
            ->get(route('admin.transactions'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.contact_us.list'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.orders.index', ['orderType' => 'event']))
            ->assertForbidden();
    }

    public function test_staff_cannot_open_role_permission_administration(): void
    {
        $staff = User::factory()->make([
            'role' => USER_ROLE_STAFF,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($staff)
            ->get(route('admin.staffs.roles.index'))
            ->assertForbidden();
    }
}
