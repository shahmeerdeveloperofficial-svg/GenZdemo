<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseBatchMembership;
use App\Models\CourseEnrollment;
use App\Models\CourseLectureFinish;
use App\Models\CourseResource;
use App\Models\LmsLessonProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class LmsEnrollmentService
{
    public function enroll(int $userId, int $courseId, string $source = 'free', ?int $paymentId = null, ?int $batchId = null, $expiresAt = null): CourseEnrollment
    {
        return DB::transaction(function () use ($userId,$courseId,$source,$paymentId,$batchId,$expiresAt) {
            $course = Course::whereKey($courseId)->lockForUpdate()->firstOrFail();
            if ($course->status != STATUS_ACTIVE || (Schema::hasColumn('courses','publication_state') && $course->publication_state !== 'published')) {
                throw ValidationException::withMessages(['course' => __('This course is not open for enrollment.')]);
            }
            $extended = Schema::hasColumn('course_enrollments','access_expires_at');
            $expiresAt = $expiresAt ? now()->parse($expiresAt) : null;
            if ($expiresAt && !$extended) throw ValidationException::withMessages(['access_expires_at'=>__('Apply the LMS enrollment migration before setting access expiry.')]);
            if ($expiresAt && $expiresAt->isPast()) throw ValidationException::withMessages(['access_expires_at' => __('Enrollment access must expire in the future.')]);

            $existing = CourseEnrollment::where('user_id',$userId)->where('course_id',$courseId)->where('status',STATUS_ACTIVE)->orderByDesc('id')->lockForUpdate()->first();
            if ($existing && (!$extended || !$existing->access_expires_at || $existing->access_expires_at->isFuture())) {
                if ($batchId) $this->joinBatch($existing, $batchId);
                return $existing;
            }
            if ($existing) $existing->update(array_filter(['status'=>STATUS_DEACTIVATE,'expired_at'=>Schema::hasColumn('course_enrollments','expired_at') ? now() : null], fn ($v) => $v !== null));

            $activeQuery = CourseEnrollment::where('course_id',$courseId)->where('status',STATUS_ACTIVE);
            if ($extended) $activeQuery->where(fn ($q) => $q->whereNull('access_expires_at')->orWhere('access_expires_at','>',now()));
            $activeCount = $activeQuery->count();
            if ($course->capacity && $activeCount >= $course->capacity) throw ValidationException::withMessages(['course' => __('This course has reached its enrollment capacity.')]);

            $attributes = ['user_id'=>$userId,'course_id'=>$courseId,'status'=>STATUS_ACTIVE];
            if ($extended) $attributes += ['access_expires_at'=>$expiresAt,'enrollment_source'=>$source,'payment_id'=>$paymentId];
            $enrollment = CourseEnrollment::create($attributes);
            if ($batchId) $this->joinBatch($enrollment, $batchId);
            return $enrollment;
        });
    }

    public function activeEnrollment(int $enrollmentId, int $userId): CourseEnrollment
    {
        $query = CourseEnrollment::whereKey($enrollmentId)->where('user_id',$userId)->where('status',STATUS_ACTIVE);
        if (Schema::hasColumn('course_enrollments','access_expires_at')) $query->where(fn ($q) => $q->whereNull('access_expires_at')->orWhere('access_expires_at','>',now()));
        return $query->firstOrFail();
    }

    public function revoke(CourseEnrollment $enrollment, string $reason): CourseEnrollment
    {
        $updates = ['status'=>STATUS_DEACTIVATE];
        if (Schema::hasColumn('course_enrollments','revoked_at')) $updates += ['revoked_at'=>now(),'revocation_reason'=>$reason];
        $enrollment->update($updates);
        return $enrollment->fresh();
    }

    public function recordProgress(CourseEnrollment $enrollment, CourseResource $resource, int $position, int $timeSpent, bool $complete = false): ?LmsLessonProgress
    {
        if ((int) $resource->course_id !== (int) $enrollment->course_id) abort(404);
        if ((Schema::hasColumn('course_resources','publication_state') && $resource->publication_state !== 'published') || (Schema::hasColumn('course_lectures','publication_state') && $resource->lecture?->publication_state !== 'published') || (Schema::hasColumn('course_lessons','publication_state') && $resource->lesson?->publication_state !== 'published')) abort(404);
        if (!Schema::hasTable('lms_lesson_progress')) {
            if ($complete) CourseLectureFinish::firstOrCreate(['enrollment_id'=>$enrollment->id,'user_id'=>$enrollment->user_id,'course_resource_id'=>$resource->id]);
            return null;
        }
        $timeSpent = max(0,min($timeSpent,120));
        $position = max(0,$position);
        return DB::transaction(function () use ($enrollment,$resource,$position,$timeSpent,$complete) {
            $row = LmsLessonProgress::where('course_enrollment_id',$enrollment->id)->where('course_resource_id',$resource->id)->lockForUpdate()->first();
            $progress = $row ?: new LmsLessonProgress(['course_enrollment_id'=>$enrollment->id,'course_resource_id'=>$resource->id,'user_id'=>$enrollment->user_id]);
            $progress->last_position_seconds = $position;
            $progress->time_spent_seconds = min(10000000,(int) $progress->time_spent_seconds + $timeSpent);
            if ($complete && !$progress->completed_at) $progress->completed_at = now();
            $progress->save();
            if ($complete) CourseLectureFinish::firstOrCreate(['enrollment_id'=>$enrollment->id,'user_id'=>$enrollment->user_id,'course_resource_id'=>$resource->id]);
            return $progress;
        });
    }

    public function completionPercent(CourseEnrollment $enrollment): int
    {
        if (!Schema::hasTable('lms_lesson_progress')) return 0;
        $resources = CourseResource::where('course_id',$enrollment->course_id);
        if (Schema::hasColumn('course_resources','completion_required')) $resources->where('completion_required',true);
        if (Schema::hasColumn('course_resources','publication_state')) $resources->where('publication_state','published');
        $required = (clone $resources)->count();
        if ($required === 0) return 0;
        $completed = LmsLessonProgress::where('course_enrollment_id',$enrollment->id)->whereNotNull('completed_at')
            ->whereIn('course_resource_id',(clone $resources)->select('id'))->count();
        return (int) floor(min(100,($completed / $required) * 100));
    }

    private function joinBatch(CourseEnrollment $enrollment, int $batchId): void
    {
        abort_unless(Schema::hasTable('course_batches'), 503, 'Apply the LMS batch migration first.');
        $batch = CourseBatch::whereKey($batchId)->where('course_id',$enrollment->course_id)->where('status','active')->lockForUpdate()->firstOrFail();
        $existing = CourseBatchMembership::where('course_enrollment_id',$enrollment->id)->first();
        if ($existing) {
            if ((int) $existing->course_batch_id !== $batch->id) throw ValidationException::withMessages(['batch_id'=>__('This enrollment already belongs to another batch.')]);
            return;
        }
        if ($batch->starts_at && $batch->starts_at->isFuture()) { /* Future batches are valid to join. */ }
        if ($batch->ends_at && $batch->ends_at->isPast()) throw ValidationException::withMessages(['batch_id'=>__('This batch has already ended.')]);
        if ($batch->capacity && CourseBatchMembership::where('course_batch_id',$batch->id)->whereHas('enrollment',fn ($q) => $q->where('status',STATUS_ACTIVE)->where(fn ($active) => $active->whereNull('access_expires_at')->orWhere('access_expires_at','>',now())))->count() >= $batch->capacity) {
            throw ValidationException::withMessages(['batch_id'=>__('This batch has reached its capacity.')]);
        }
        CourseBatchMembership::create(['course_batch_id'=>$batch->id,'course_enrollment_id'=>$enrollment->id,'joined_at'=>now()]);
    }
}
