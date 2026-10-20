<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Services\LmsEnrollmentService;
use App\Services\LmsQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseQuizController extends Controller
{
    public function index($enrollmentId, LmsEnrollmentService $enrollments)
    {
        abort_unless(Schema::hasTable('lms_quizzes'),404);
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $quizzes = LmsQuiz::where('course_id',$enrollment->course_id)->where('status','published')->with(['attempts'=>fn($q)=>$q->where('course_enrollment_id',$enrollment->id)->orderByDesc('attempt_number')])->orderBy('opens_at')->get();
        return view('student.my_courses.quizzes',compact('enrollment','quizzes'));
    }

    public function start($enrollmentId, $quizId, LmsEnrollmentService $enrollments, LmsQuizService $service)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $quiz = LmsQuiz::where('course_id',$enrollment->course_id)->whereKey($quizId)->firstOrFail();
        $payload = $service->start($quiz,$enrollment->id);
        return view('student.my_courses.quiz-attempt', $payload + ['enrollment'=>$enrollment,'quiz'=>$quiz]);
    }

    public function submit(Request $request, $enrollmentId, $attemptId, LmsEnrollmentService $enrollments, LmsQuizService $service)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $attempt = LmsQuizAttempt::whereKey($attemptId)->where('course_enrollment_id',$enrollment->id)->firstOrFail();
        $data = $request->validate(['answers'=>['nullable','array'],'answers.*'=>['nullable','array','max:10'],'answers.*.*'=>['integer','distinct']]);
        $updated = $service->submit($attempt,$data['answers']??[]);
        return redirect()->route('student.my_courses.quizzes.result',['enrollment_id'=>encodeId($enrollment->id),'attempt_id'=>$updated->id])->with('success',__('Quiz submitted.'));
    }

    public function result($enrollmentId, $attemptId, LmsEnrollmentService $enrollments)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $attempt = LmsQuizAttempt::whereKey($attemptId)->where('course_enrollment_id',$enrollment->id)->with(['quiz','answers'])->firstOrFail();
        $questions = collect();
        if ($attempt->result_released_at) $questions = $attempt->quiz->questions()->whereIn('lms_questions.id',$attempt->question_order)->with('options')->get()->keyBy('id');
        return view('student.my_courses.quiz-result',compact('enrollment','attempt','questions'));
    }
}
