<?php

namespace Database\Seeders;

use App\Models\ConsulterFeedback;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoConsultationReviewSeeder extends Seeder
{
    private const REVIEWS = [
        'DEMO ONLY: The sample counselor explained the fictional study-planning workflow clearly.',
        'DEMO ONLY: This fictional example shows how a student review appears in the admin queue.',
        'DEMO ONLY: Sample feedback for testing consultation review status and listing actions.',
        'DEMO ONLY: Fictional review text; it is not a real student endorsement or service rating.',
    ];

    public function run(): void
    {
        $database = DB::connection()->getDatabaseName();
        $safeLocalDatabase = in_array($database, ['gen_soft_run', 'genz_crm_lms_demo', 'demo'], true);

        if (app()->environment('production') || (!app()->environment('testing') && (!app()->environment('local') || !$safeLocalDatabase))) {
            $this->command?->error('Demo consultation reviews are restricted to the local demo database. No records were changed.');
            return;
        }

        $consultant = User::firstOrCreate(
            ['email' => 'demo.consultation.reviews@genzconsultants.example'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Counselor',
                'password' => Hash::make(Str::random(48)),
                'role' => USER_ROLE_CONSULTANT,
                'status' => USER_STATUS_ACTIVE,
                'google_auth_status' => 0,
            ]
        );

        if ((int) $consultant->role !== USER_ROLE_CONSULTANT) {
            $this->command?->error('The reserved demo counselor email belongs to a different role. No reviews were added.');
            return;
        }

        $students = User::query()
            ->where('role', USER_ROLE_STUDENT)
            ->where('email', 'like', 'demo.learner.%@genzconsultants.example')
            ->orderBy('id')
            ->get();

        $added = 0;
        foreach ($students as $index => $student) {
            $comment = self::REVIEWS[$index % count(self::REVIEWS)];
            $existing = ConsulterFeedback::withTrashed()
                ->where('consulter_id', $consultant->id)
                ->where('student_id', $student->id)
                ->where('comment', $comment)
                ->exists();

            if ($existing) {
                continue;
            }

            ConsulterFeedback::create([
                'consulter_id' => $consultant->id,
                'student_id' => $student->id,
                'appointment_id' => null,
                'comment' => $comment,
                'status' => $index % 3 === 1 ? STATUS_PENDING : STATUS_ACTIVE,
            ]);
            $added++;
        }

        $this->command?->info("Added {$added} fictional consultation reviews for {$students->count()} demo students.");
    }
}
