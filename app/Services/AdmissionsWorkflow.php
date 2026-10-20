<?php

namespace App\Services;

use App\Models\AcademicProgram;
use App\Models\AdmissionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\ProgramIntake;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdmissionsWorkflow
{
    private const TRANSITIONS = [
        'draft' => ['document_collection','withdrawn'],
        'document_collection' => ['internal_review','withdrawn'],
        'internal_review' => ['document_collection','ready_to_submit','withdrawn'],
        'ready_to_submit' => ['submitted','document_collection','withdrawn'],
        'submitted' => ['university_review','conditional_offer','unconditional_offer','rejected','withdrawn'],
        'university_review' => ['conditional_offer','unconditional_offer','rejected','withdrawn'],
        'conditional_offer' => ['accepted','rejected','withdrawn'],
        'unconditional_offer' => ['accepted','rejected','withdrawn'],
        'accepted' => ['enrolled','withdrawn'],
        'rejected' => [], 'withdrawn' => [], 'enrolled' => [],
    ];

    public function createForStudent(User $student, AcademicProgram $program, ?ProgramIntake $intake): AdmissionApplication
    {
        return DB::transaction(function () use ($student, $program, $intake) {
            User::whereKey($student->id)->lockForUpdate()->firstOrFail();
            $duplicate = AdmissionApplication::where('student_id', $student->id)
                ->where('academic_program_id', $program->id)->whereIn('status', AdmissionApplication::ACTIVE_STATUSES)
                ->where(function ($query) use ($intake) {
                    $intake ? $query->where('program_intake_id', $intake->id) : $query->whereNull('program_intake_id');
                })->exists();
            if ($duplicate) throw ValidationException::withMessages(['program_id' => 'You already have an active application for this program and intake.']);

            $application = AdmissionApplication::create([
                'reference_code' => 'PENDING-'.bin2hex(random_bytes(8)), 'student_id' => $student->id,
                'university_id' => $program->university_id, 'academic_program_id' => $program->id,
                'program_intake_id' => $intake?->id, 'application_deadline' => $intake?->application_deadline,
                'status' => 'draft', 'visa_status' => 'not_started',
            ]);
            $application->reference_code = 'GZ-'.now()->format('Y').'-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT);
            $application->save();
            $application->history()->create(['from_status' => null, 'to_status' => 'draft', 'actor_id' => $student->id, 'reason' => 'Student started an application.']);
            return $application;
        });
    }

    public function transition(AdmissionApplication $application, string $to, User $actor, ?string $reason = null): AdmissionApplication
    {
        return DB::transaction(function () use ($application, $to, $actor, $reason) {
            $locked = AdmissionApplication::whereKey($application->id)->lockForUpdate()->firstOrFail();
            if (!in_array($to, self::TRANSITIONS[$locked->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => "The transition from {$locked->status} to {$to} is not allowed."]);
            }
            if (in_array($to, ['rejected','withdrawn'], true) && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'A reason is required for rejection or withdrawal.']);
            }
            $from = $locked->status;
            $locked->status = $to;
            if ($to === 'submitted') $locked->submitted_at = now();
            if (in_array($to, ['rejected','withdrawn','enrolled'], true)) $locked->closed_at = now();
            $locked->save();
            ApplicationStatusHistory::create(['admission_application_id' => $locked->id, 'from_status' => $from, 'to_status' => $to, 'actor_id' => $actor->id, 'reason' => $reason]);
            return $locked->fresh();
        });
    }
}
