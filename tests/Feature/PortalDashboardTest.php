<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class PortalDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_live_kpis_and_explicit_empty_states(): void
    {
        $admin = User::factory()->make([
            'id' => 1,
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Operations overview')
            ->assertDontSee('dashboard-updated-at')
            ->assertDontSee('dashboard-refresh')
            ->assertDontSee('Quick access')
            ->assertDontSee('dashboard-quick-access-heading')
            ->assertSee('Live figures are calculated from records')
            ->assertSee('Student directory')
            ->assertSee('Most popular courses')
            ->assertSee('dashboard-enrollment-chart')
            ->assertSee('genz-toast-host')
            ->assertSee('genz/js/backend-toast.js')
            ->assertSee('genz/js/backend-shell.js')
            ->assertSee('genz-admin-shell')
            ->assertSee('genz-brand__fallback')
            ->assertSee('genz-theme-toggle')
            ->assertSee('data-genz-sidebar-toggle')
            ->assertSee('genz-sidebar-logout')
            ->assertSee('fa-filter-circle-dollar')
            ->assertSee('fa-chart-line')
            ->assertSee('genz-brand--compact');

        $this->actingAs($admin)
            ->get(route('admin.dashboard.metrics'))
            ->assertOk()
            ->assertJsonStructure(['kpis', 'enrollmentTrend' => ['labels', 'values'], 'leadPipeline' => ['labels', 'values'], 'admissionsPipeline' => ['labels', 'values'], 'coursePopularity' => ['labels', 'values'], 'updatedAt']);
    }

    public function test_student_cannot_open_the_admin_dashboard(): void
    {
        $student = User::factory()->make([
            'id' => 2,
            'role' => USER_ROLE_STUDENT,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_instructor_portal_renders_real_counts_and_empty_state(): void
    {
        $instructor = User::factory()->make([
            'id' => 3,
            'role' => USER_ROLE_INSTRUCTOR,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee('Courses assigned to you')
            ->assertSee('Nothing assigned yet')
            ->assertSee('genz-theme-toggle')
            ->assertSee('data-genz-sidebar-toggle')
            ->assertSee('genz-sidebar-logout')
            ->assertSee('fa-chalkboard-user');
    }
}
