<?php

namespace App\Services;

use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsGrade;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LmsAssignmentService
{
    public function saveSubmission(LmsAssignment $assignment, int $enrollmentId, int $userId, ?string $text, ?int $fileId, bool $submit): LmsAssignmentSubmission
    {
        return DB::transaction(function () use ($assignment,$enrollmentId,$userId,$text,$fileId,$submit) {
            $assignment = LmsAssignment::whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            if ($assignment->status !== 'published') throw ValidationException::withMessages(['assignment'=>__('This assignment is not available.')]);
            if ($submit && trim((string)$text)==='' && !$fileId) throw ValidationException::withMessages(['submission'=>__('Provide written work or upload a file before submitting.')]);
            $existingDraft = LmsAssignmentSubmission::where('lms_assignment_id',$assignment->id)->where('course_enrollment_id',$enrollmentId)->where('status','draft')->latest('version')->lockForUpdate()->first();
            $late = $assignment->due_at && now()->gt($assignment->due_at);
            if ($submit && $late && $assignment->late_policy==='reject') throw ValidationException::withMessages(['submission'=>__('The deadline has passed; late submissions are closed.')]);
            $submittedCount = LmsAssignmentSubmission::where('lms_assignment_id',$assignment->id)->where('course_enrollment_id',$enrollmentId)->whereIn('status',['submitted','late','graded'])->count();
            if ($submit && !$existingDraft && $submittedCount >= $assignment->max_submissions) throw ValidationException::withMessages(['submission'=>__('You have used all permitted submissions.')]);
            if ($existingDraft) $submission = $existingDraft;
            else $submission = new LmsAssignmentSubmission(['lms_assignment_id'=>$assignment->id,'course_enrollment_id'=>$enrollmentId,'user_id'=>$userId,'version'=>(int)LmsAssignmentSubmission::where('lms_assignment_id',$assignment->id)->where('course_enrollment_id',$enrollmentId)->max('version')+1]);
            $submission->text_response = $text; $submission->file_manager_id = $fileId;
            $submission->status = $submit ? ($late?'late':'submitted') : 'draft';
            $submission->is_late = $submit && $late; $submission->submitted_at = $submit ? now() : null;
            $submission->save();
            return $submission;
        });
    }

    public function grade(LmsAssignmentSubmission $submission, int $graderId, float $rawScore, ?array $rubric, ?string $feedback, bool $release): LmsGrade
    {
        return DB::transaction(function () use ($submission,$graderId,$rawScore,$rubric,$feedback,$release) {
            $submission = LmsAssignmentSubmission::whereKey($submission->id)->lockForUpdate()->firstOrFail();
            if (!in_array($submission->status,['submitted','late','graded'],true)) throw ValidationException::withMessages(['submission'=>__('Only submitted work can be graded.')]);
            $assignment = LmsAssignment::whereKey($submission->lms_assignment_id)->firstOrFail();
            if ($rawScore < 0 || $rawScore > $assignment->max_points) throw ValidationException::withMessages(['raw_score'=>__('Score must be between zero and the assignment maximum.')]);
            $score = $submission->is_late ? round($rawScore * (1 - ($assignment->late_penalty_percent / 100)),2) : round($rawScore,2);
            $grade = LmsGrade::where('lms_assignment_submission_id',$submission->id)->lockForUpdate()->first();
            $before = $grade ? $grade->only(['raw_score','score','max_points','rubric','feedback','released_at']) : null;
            $grade ??= new LmsGrade(['lms_assignment_submission_id'=>$submission->id]);
            $grade->grader_id = $graderId; $grade->raw_score = $rawScore; $grade->score = $score; $grade->max_points = $assignment->max_points;
            $grade->rubric = $rubric; $grade->feedback = $feedback; if ($release) $grade->released_at ??= now(); else $grade->released_at = null;
            $grade->save();
            $historyId=DB::table('lms_grade_history')->insertGetId(['lms_grade_id'=>$grade->id,'actor_id'=>$graderId,'before_values'=>$before ? json_encode($before) : null,'after_values'=>json_encode($grade->only(['raw_score','score','max_points','rubric','feedback','released_at'])),'created_at'=>now(),'updated_at'=>now()]);
            $submission->update(['status'=>'graded']);
            if ($release) app(NotificationDeliveryService::class)->enqueue('lms-assignment-grade-history-'.$historyId,$submission->user_id,'lms.assignment.graded',__('Assignment graded'),__('Your submission for :assignment has been graded.', ['assignment'=>$assignment->title]),route('student.my_courses.assignments.index',encodeId($submission->course_enrollment_id)));
            return $grade;
        });
    }
}
