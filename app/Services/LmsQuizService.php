<?php

namespace App\Services;

use App\Models\LmsAnswer;
use App\Models\LmsQuestion;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LmsQuizService
{
    public function start(LmsQuiz $quiz, int $enrollmentId): array
    {
        return DB::transaction(function () use ($quiz,$enrollmentId) {
            $quiz = LmsQuiz::whereKey($quiz->id)->lockForUpdate()->firstOrFail();
            if ($quiz->status !== 'published') throw ValidationException::withMessages(['quiz'=>__('This quiz is not available.')]);
            if ($quiz->opens_at && now()->lt($quiz->opens_at)) throw ValidationException::withMessages(['quiz'=>__('This quiz has not opened yet.')]);
            if ($quiz->closes_at && now()->gt($quiz->closes_at)) throw ValidationException::withMessages(['quiz'=>__('This quiz is closed.')]);
            $active = LmsQuizAttempt::where('lms_quiz_id',$quiz->id)->where('course_enrollment_id',$enrollmentId)->where('status','in_progress')->lockForUpdate()->first();
            if ($active && (!$active->deadline_at || !now()->greaterThan($active->deadline_at))) return $this->attemptPayload($active);
            if ($active) $active->update(['status'=>'expired','submitted_at'=>now()]);
            $used = LmsQuizAttempt::where('lms_quiz_id',$quiz->id)->where('course_enrollment_id',$enrollmentId)->count();
            if ($used >= $quiz->max_attempts) throw ValidationException::withMessages(['quiz'=>__('You have used all attempts for this quiz.')]);
            $questionIds = $quiz->questions()->where('lms_questions.status','published')->pluck('lms_questions.id')->all();
            if (!$questionIds) throw ValidationException::withMessages(['quiz'=>__('This quiz has no published questions.')]);
            if ($quiz->shuffle_questions) shuffle($questionIds);
            $deadline = $quiz->time_limit_seconds ? now()->addSeconds($quiz->time_limit_seconds) : null;
            if ($quiz->closes_at && (!$deadline || $quiz->closes_at->lt($deadline))) $deadline = $quiz->closes_at;
            $attempt = LmsQuizAttempt::create(['lms_quiz_id'=>$quiz->id,'course_enrollment_id'=>$enrollmentId,'attempt_number'=>$used+1,'status'=>'in_progress','question_order'=>$questionIds,'started_at'=>now(),'deadline_at'=>$deadline]);
            return $this->attemptPayload($attempt);
        });
    }

    public function submit(LmsQuizAttempt $attempt, array $submitted): LmsQuizAttempt
    {
        $result = DB::transaction(function () use ($attempt,$submitted) {
            $attempt = LmsQuizAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            if ($attempt->status !== 'in_progress') return $attempt;
            if ($attempt->deadline_at && now()->greaterThan($attempt->deadline_at)) {
                $attempt->update(['status'=>'expired','submitted_at'=>now()]);
                return $attempt->fresh();
            }
            $quiz = LmsQuiz::whereKey($attempt->lms_quiz_id)->firstOrFail();
            $questions = LmsQuestion::whereIn('id',$attempt->question_order)->where('course_id',$quiz->course_id)->where('status','published')->with('options')->get()->keyBy('id');
            $score = 0; $total = 0;
            foreach ($attempt->question_order as $questionId) {
                $question = $questions->get($questionId);
                if (!$question) throw ValidationException::withMessages(['answers'=>__('Quiz content changed during the attempt. Please contact an instructor.')]);
                $total += (int) $question->points;
                $selected = array_values(array_unique(array_map('intval',$submitted[$questionId] ?? [])));
                $validOptionIds = $question->options->pluck('id')->all();
                if (array_diff($selected,$validOptionIds)) throw ValidationException::withMessages(['answers'=>__('An answer option does not belong to its question.')]);
                if ($question->question_type === 'single' && count($selected) > 1) throw ValidationException::withMessages(['answers'=>__('Choose only one option for this question.')]);
                $correct = $question->options->where('is_correct',true)->pluck('id')->map(fn ($id)=>(int)$id)->sort()->values()->all();
                $normalized = $selected; sort($normalized);
                $awarded = $normalized === $correct ? (int) $question->points : 0;
                $score += $awarded;
                LmsAnswer::updateOrCreate(['lms_quiz_attempt_id'=>$attempt->id,'lms_question_id'=>$question->id],['selected_option_ids'=>$selected,'awarded_points'=>$awarded]);
            }
            $percentage = $total ? round(($score / $total) * 100,2) : 0;
            $attempt->update(['status'=>'submitted','submitted_at'=>now(),'score'=>$score,'total_points'=>$total,'percentage'=>$percentage,'passed'=>$percentage >= $quiz->pass_percent,'result_released_at'=>$quiz->manual_result_release ? null : now()]);
            return $attempt->fresh();
        });
        if ($result->status === 'expired') throw ValidationException::withMessages(['quiz'=>__('The attempt time limit has expired; this attempt is closed.')]);
        return $result;
    }

    public function releaseResult(LmsQuizAttempt $attempt): LmsQuizAttempt
    {
        abort_unless($attempt->status === 'submitted',409);
        $attempt->update(['result_released_at'=>$attempt->result_released_at ?? now()]);
        return $attempt->fresh();
    }

    public function attemptPayload(LmsQuizAttempt $attempt): array
    {
        $questions = LmsQuestion::whereIn('id',$attempt->question_order)->where('status','published')->with(['options'=>fn ($q) => $q->select(['id','lms_question_id','option_text','sort_order'])])->get()->keyBy('id');
        return ['attempt'=>$attempt,'questions'=>collect($attempt->question_order)->map(fn ($id) => $questions->get($id))->filter()->values()];
    }
}
