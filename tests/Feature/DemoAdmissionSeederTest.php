<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\User;
use Database\Seeders\DemoAdmissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoAdmissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_an_idempotent_fictional_admission_and_status_history(): void
    {
        $student = User::create([
            'first_name' => 'Demo',
            'last_name' => 'Learner',
            'email' => 'demo.learner.admissions-test@genzconsultants.example',
            'mobile' => '+10000000000',
            'password' => bcrypt('DemoPass!2026'),
            'role' => USER_ROLE_STUDENT,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'Demo Country', 'slug' => 'demo-country', 'status' => STATUS_ACTIVE,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $universityId = DB::table('universities')->insertGetId([
            'name' => 'Demo University', 'slug' => 'demo-university', 'country_id' => (string) $countryId,
            'avg_cost' => 0, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('academic_programs')->insert([
            'university_id' => $universityId, 'country_id' => $countryId,
            'title' => 'Demo Sample Program', 'slug' => 'toronto-bachelor-computer-science-sample',
            'status' => 'published', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->seed(DemoAdmissionSeeder::class);

        $application = AdmissionApplication::where('student_id', $student->id)->firstOrFail();
        $this->assertSame('GZ-DEMO-ADM-001', $application->reference_code);
        $this->assertSame('document_collection', $application->status);
        $this->assertSame('not_started', $application->visa_status);
        $this->assertNull($application->counselor_id);
        $this->assertSame($student->id, (int) ApplicationStatusHistory::where('admission_application_id', $application->id)->value('actor_id'));

        $this->seed(DemoAdmissionSeeder::class);
        $this->assertSame(1, AdmissionApplication::where('student_id', $student->id)->count());
        $this->assertSame(1, ApplicationStatusHistory::where('admission_application_id', $application->id)->count());
    }
}
