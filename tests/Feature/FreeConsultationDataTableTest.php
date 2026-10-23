<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FreeConsultationDataTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultation_name_column_can_be_sorted(): void
    {
        DB::table('free_consultations')->insert([
            [
                'first_name' => 'Zara', 'last_name' => 'Ahmed', 'email' => 'zara@example.test',
                'country_ids' => '[]', 'study_level_id' => 1, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'first_name' => 'Amina', 'last_name' => 'Khan', 'email' => 'amina@example.test',
                'country_ids' => '[]', 'study_level_id' => 1, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        $admin = User::factory()->make([
            'id' => 1,
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.free_consultations.index', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'order' => [['column' => 1, 'dir' => 'asc']],
                'columns' => [
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                    ['data' => 'name', 'name' => 'name', 'orderable' => 'true', 'searchable' => 'true'],
                    ['data' => 'email', 'name' => 'email', 'orderable' => 'true', 'searchable' => 'true'],
                ],
            ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Amina Khan');
    }
}
