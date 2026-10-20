<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\LmsCertificate;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LmsCertificateService
{
    public function eligibility(CourseEnrollment $enrollment): array
    {
        $reasons = [];
        if ((int)$enrollment->status !== (int)STATUS_ACTIVE || ($enrollment->access_expires_at && $enrollment->access_expires_at->isPast())) $reasons[] = __('Enrollment is not active.');
        if (app(LmsEnrollmentService::class)->completionPercent($enrollment) < 100) $reasons[] = __('All required course lessons must be completed.');
        if (\Illuminate\Support\Facades\Schema::hasTable('lms_quizzes')) {
            foreach (LmsQuiz::where('course_id',$enrollment->course_id)->where('status','published')->get() as $quiz) {
                $passed = LmsQuizAttempt::where('lms_quiz_id',$quiz->id)->where('course_enrollment_id',$enrollment->id)->where('passed',true)
                    ->where(fn($q)=>!$quiz->manual_result_release ? $q : $q->whereNotNull('result_released_at'))->exists();
                if (!$passed) $reasons[] = __('Pass the required quiz: :title', ['title'=>$quiz->title]);
            }
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('lms_assignments')) {
            foreach (LmsAssignment::where('course_id',$enrollment->course_id)->where('status','published')->get() as $assignment) {
                $graded = LmsAssignmentSubmission::where('lms_assignment_id',$assignment->id)->where('course_enrollment_id',$enrollment->id)->whereHas('grade',fn($q)=>$q->whereNotNull('released_at'))->exists();
                if (!$graded) $reasons[] = __('Submit and receive a released grade for: :title', ['title'=>$assignment->title]);
            }
        }
        return ['eligible'=>count($reasons)===0,'reasons'=>$reasons];
    }

    public function issue(CourseEnrollment $enrollment, ?int $issuerId): LmsCertificate
    {
        return DB::transaction(function () use ($enrollment,$issuerId) {
            $locked = CourseEnrollment::whereKey($enrollment->id)->lockForUpdate()->firstOrFail();
            $eligibility = $this->eligibility($locked);
            if (!$eligibility['eligible']) throw ValidationException::withMessages(['eligibility'=>implode(' ', $eligibility['reasons'])]);
            $certificate = LmsCertificate::firstOrCreate(['course_enrollment_id'=>$locked->id],[
                'course_id'=>$locked->course_id,'user_id'=>$locked->user_id,
                'certificate_number'=>'GZ-'.now()->format('Y').'-'.Str::upper(Str::random(10)),
                'verification_token'=>Str::random(48),'issued_at'=>now(),'issued_by'=>$issuerId,
            ]);
            if ($certificate->wasRecentlyCreated) app(NotificationDeliveryService::class)->enqueue('lms-certificate-'.$certificate->id,$certificate->user_id,'lms.certificate.issued',__('Course certificate issued'),__('Your certificate for :course is ready.', ['course'=>$certificate->course?->title ?? __('your course')]),route('student.my_courses.certificates.show',$certificate->id));
            return $certificate;
        });
    }

    public function revoke(LmsCertificate $certificate, int $actorId, string $reason): LmsCertificate
    {
        if (!$certificate->revoked_at) $certificate->update(['revoked_at'=>now(),'revoked_by'=>$actorId,'revocation_reason'=>$reason]);
        return $certificate->fresh();
    }
}
