<?php

namespace Tests\Feature;

use App\Models\ConsulterFeedback;
use App\Models\User;
use Database\Seeders\DemoConsultationReviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoConsultationReviewSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_clearly_labeled_reviews_for_demo_students_without_changing_existing_users(): void
    {
        $student = User::create([
            'first_name' => 'Demo',
            'last_name' => 'Learner',
            'email' => 'demo.learner.review-test@genzconsultants.example',
            'mobile' => '+10000000000',
            'password' => bcrypt('DemoPass!2026'),
            'role' => USER_ROLE_STUDENT,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
        $existingConsultant = User::create([
            'first_name' => 'Existing',
            'last_name' => 'Consultant',
            'email' => 'existing-consultant@example.test',
            'mobile' => '+10000000001',
            'password' => bcrypt('DemoPass!2026'),
            'role' => USER_ROLE_CONSULTANT,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->seed(DemoConsultationReviewSeeder::class);

        $review = ConsulterFeedback::where('student_id', $student->id)->firstOrFail();
        $demoConsultant = User::findOrFail($review->consulter_id);
        $this->assertSame(USER_ROLE_CONSULTANT, (int) $demoConsultant->role);
        $this->assertStringContainsString('@genzconsultants.example', $demoConsultant->email);
        $this->assertNotSame($existingConsultant->id, $demoConsultant->id);
        $this->assertStringStartsWith('DEMO ONLY:', $review->comment);
        $this->assertNull($review->appointment_id);

        $this->seed(DemoConsultationReviewSeeder::class);
        $this->assertSame(1, ConsulterFeedback::where('student_id', $student->id)->count());
    }
}
