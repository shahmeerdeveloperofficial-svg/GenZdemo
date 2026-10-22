<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SidebarFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_navigation_follows_the_operational_journey(): void
    {
        $admin = User::factory()->make([
            'id' => 1,
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Consultation Requests')
            ->assertSee('Study Abroad Catalogue')
            ->assertSee('Course Tracks')
            ->assertSee('Roles &amp; Permissions', false)
            ->assertDontSee('Quick access');

        $html = $response->getContent();
        $navigationStart = strpos($html, 'id="sidebarMenu"');
        $this->assertNotFalse($navigationStart, 'The sidebar navigation list should render.');

        $sequence = [
            'Dashboard',
            'Lead CRM',
            'Consultation Requests',
            'Student Directory',
            'Student Documents',
            'Study Abroad Catalogue',
            'Admissions',
            'Course Tracks',
            'Manage Services',
            'Transactions',
            'Analytics &amp; Reports',
            'Team &amp; Permissions',
            'Website Content &amp; SEO',
            'Notifications',
            'Profile &amp; Security',
        ];

        $lastPosition = $navigationStart;
        foreach ($sequence as $label) {
            $position = strpos($html, $label, $lastPosition);
            $this->assertNotFalse($position, "Expected sidebar item '{$label}' to render in sequence.");
            $this->assertGreaterThan($lastPosition, $position, "Sidebar item '{$label}' should follow the previous item.");
            $lastPosition = $position;
        }
    }

    public function test_staff_with_staff_management_access_does_not_see_super_admin_role_controls(): void
    {
        $this->seed(PermissionSeeder::class);

        $role = Role::create([
            'name' => 'staff-directory-' . Str::uuid(),
            'guard_name' => 'web',
            'status' => STATUS_ACTIVE,
        ]);
        $role->syncPermissions(['Manage Staffs']);

        $staff = User::create([
            'first_name' => 'Team',
            'last_name' => 'Member',
            'email' => 'sidebar-staff-' . Str::uuid() . '@example.test',
            'password' => bcrypt(Str::random(24)),
            'role' => USER_ROLE_STAFF,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
        $staff->assignRole($role);

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Staff Directory')
            ->assertDontSee('Roles &amp; Permissions', false);
    }
}
