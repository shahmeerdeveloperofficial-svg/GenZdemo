<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CourseCurriculumTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_course_lessons_when_course_benefits_are_empty(): void
    {
        DB::table('programs')->insert([
            'id' => 1,
            'title' => 'Demo learning',
            'slug' => 'demo-learning',
            'top_section' => '{}',
            'step_section' => '{}',
            'created_by' => 1,
            'status' => STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $course = Course::create([
            'program_id' => 1,
            'title' => 'Course with no benefits yet',
            'slug' => 'course-with-no-benefits-yet',
            'duration' => 4,
            'price' => 0,
            'course_benefits' => null,
            'faqs' => [],
            'instructors' => [],
            'created_by' => 1,
            'status' => STATUS_ACTIVE,
        ]);

        $admin = User::factory()->make([
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.lessons.index', encodeId($course->id)))
            ->assertOk()
            ->assertSee('Course with no benefits yet');
    }
}
