<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramDataTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_listing_ajax_returns_a_datatables_json_payload(): void
    {
        $admin = User::factory()->make([
            'id' => 1,
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.courses.programs.index', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'order' => [['column' => 1, 'dir' => 'asc']],
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'title', 'name' => 'title', 'orderable' => 'true', 'searchable' => 'true'],
                    ['data' => 'status', 'name' => 'status', 'orderable' => 'true', 'searchable' => 'false'],
                    ['data' => 'action', 'name' => 'action', 'orderable' => 'false', 'searchable' => 'false'],
                ],
            ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }
}
