<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use App\Models\AdmissionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoAdmissionSeeder extends Seeder
{
    private const STATUSES = ['document_collection', 'internal_review', 'ready_to_submit', 'draft'];

    public function run(): void
    {
        $database = DB::connection()->getDatabaseName();
        $safeLocalDatabase = in_array($database, ['gen_soft_run', 'genz_crm_lms_demo', 'demo'], true);

        if (app()->environment('production') || (!app()->environment('testing') && (!app()->environment('local') || !$safeLocalDatabase))) {
            $this->command?->error('Demo admissions are restricted to the local demo database. No records were changed.');
            return;
        }

        $students = User::query()
            ->where('role', USER_ROLE_STUDENT)
            ->where('email', 'like', 'demo.learner.%@genzconsultants.example')
            ->orderBy('id')
            ->get();
        $programs = AcademicProgram::query()
            ->whereIn('slug', [
                'toronto-bachelor-computer-science-sample',
                'melbourne-master-data-science-sample',
                'manchester-mechanical-engineering-program-sample',
            ])
            ->where('status', 'published')
            ->whereHas('university', fn ($query) => $query->where('status', STATUS_ACTIVE))
            ->orderBy('id')
            ->get();

        if ($students->isEmpty() || $programs->isEmpty()) {
            $this->command?->warn('No demo students or published sample programs were found. Add no admissions; check demo student and catalogue seeders.');
            return;
        }

        $added = 0;
        foreach ($students as $index => $student) {
            $program = $programs[$index % $programs->count()];
            $reference = 'GZ-DEMO-ADM-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

            $referenceExists = AdmissionApplication::withTrashed()->where('reference_code', $reference)->exists();
            $studentProgramExists = AdmissionApplication::withTrashed()
                ->where('student_id', $student->id)
                ->where('academic_program_id', $program->id)
                ->exists();
            if ($referenceExists || $studentProgramExists) {
                continue;
            }

            $status = self::STATUSES[$index % count(self::STATUSES)];
            $application = DB::transaction(function () use ($student, $program, $reference, $status) {
                $application = AdmissionApplication::create([
                    'reference_code' => $reference,
                    'student_id' => $student->id,
                    'university_id' => $program->university_id,
                    'academic_program_id' => $program->id,
                    'program_intake_id' => null,
                    'counselor_id' => null,
                    'application_deadline' => today()->addDays(30 + ($student->id % 30)),
                    'status' => $status,
                    'visa_status' => 'not_started',
                ]);

                ApplicationStatusHistory::create([
                    'admission_application_id' => $application->id,
                    'from_status' => null,
                    'to_status' => $status,
                    'actor_id' => $student->id,
                    'reason' => 'Fictional demo record created for local admissions workflow testing; no university submission or decision is implied.',
                ]);

                return $application;
            });

            if ($application) {
                $added++;
            }
        }

        $this->command?->info("Added {$added} fictional admissions for {$students->count()} demo students. No real university decisions are represented.");
    }
}
