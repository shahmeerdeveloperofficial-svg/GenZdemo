<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LmsQuestion;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Services\LmsQuizService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CourseQuizController extends Controller
{
    use ResponseTrait;

    private function course($id): Course
    {
        $query = Course::whereKey((int)$id);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $query->where('created_by',auth()->id());
        return $query->firstOrFail();
    }

    public function index($courseId)
    {
        $course = $this->course($courseId);
        $quizzes = LmsQuiz::where('course_id',$course->id)->with(['questions.options','attempts'])->orderByDesc('id')->get();
        $banks = DB::table('lms_question_banks')->where('course_id',$course->id)->get();
        $questions = LmsQuestion::where('course_id',$course->id)->with('options')->orderByDesc('id')->get();
        return view('admin.courses.quizzes.index',compact('course','quizzes','banks','questions'));
    }

    public function storeBank(Request $request, $courseId)
    {
        $course = $this->course($courseId);
        $data = $request->validate(['title'=>['required','string','max:180']]);
        DB::table('lms_question_banks')->insert(['course_id'=>$course->id,'title'=>$data['title'],'created_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);
        return back()->with('success',__('Question bank created.'));
    }

    public function storeQuiz(Request $request, $courseId)
    {
        $course = $this->course($courseId);
        $data = $request->validate([
            'title'=>['required','string','max:180'],'description'=>['nullable','string','max:5000'],
            'opens_at'=>['nullable','date'],'closes_at'=>['nullable','date','after:opens_at'],
            'time_limit_seconds'=>['nullable','integer','min:60','max:86400'],'max_attempts'=>['required','integer','min:1','max:20'],
            'pass_percent'=>['required','integer','min:1','max:100'],'shuffle_questions'=>['nullable','boolean'],
            'manual_result_release'=>['nullable','boolean'],
        ]);
        LmsQuiz::create(['course_id'=>$course->id,'title'=>$data['title'],'description'=>$data['description']??null,'status'=>'draft',
            'opens_at'=>$data['opens_at']??null,'closes_at'=>$data['closes_at']??null,'time_limit_seconds'=>$data['time_limit_seconds']??null,
            'max_attempts'=>$data['max_attempts'],'pass_percent'=>$data['pass_percent'],'shuffle_questions'=>(bool)($data['shuffle_questions']??false),
            'manual_result_release'=>(bool)($data['manual_result_release']??false),'created_by'=>auth()->id()]);
        return back()->with('success',__('Quiz saved as draft. Add questions, then publish.'));
    }

    public function storeQuestion(Request $request, $courseId)
    {
        $course = $this->course($courseId);
        $data = $request->validate([
            'question_bank_id'=>['nullable','integer',Rule::exists('lms_question_banks','id')->where('course_id',$course->id)],
            'question_text'=>['required','string','max:10000'],'question_type'=>['required',Rule::in(['single','multiple'])],
            'explanation'=>['nullable','string','max:5000'],'points'=>['required','integer','min:1','max:1000'],
            'options'=>['required','array','min:2','max:10'],'options.*.text'=>['required','string','max:1000'],'options.*.correct'=>['nullable','boolean'],
        ]);
        $correctCount = collect($data['options'])->filter(fn ($option) => !empty($option['correct']))->count();
        if ($correctCount < 1 || ($data['question_type']==='single' && $correctCount !== 1)) return back()->withInput()->withErrors(['options'=>__('Single-choice requires exactly one correct answer; multiple-choice requires at least one.')]);
        DB::transaction(function () use ($course,$data) {
            $question = LmsQuestion::create(['course_id'=>$course->id,'question_bank_id'=>$data['question_bank_id']??null,'question_text'=>$data['question_text'],'question_type'=>$data['question_type'],'explanation'=>$data['explanation']??null,'points'=>$data['points'],'status'=>'published','created_by'=>auth()->id()]);
            foreach ($data['options'] as $index=>$option) $question->options()->create(['option_text'=>$option['text'],'is_correct'=>!empty($option['correct']),'sort_order'=>$index]);
        });
        return back()->with('success',__('Question saved to the bank.'));
    }

    public function linkQuestion(Request $request, $courseId, $quizId)
    {
        $course = $this->course($courseId);
        $quiz = LmsQuiz::where('course_id',$course->id)->whereKey($quizId)->firstOrFail();
        abort_if($quiz->status==='published',409,'Unpublish the quiz before changing its questions.');
        $data = $request->validate(['question_id'=>['required','integer',Rule::exists('lms_questions','id')->where('course_id',$course->id)->where('status','published')]]);
        $quiz->questions()->syncWithoutDetaching([$data['question_id']=>['sort_order'=>$quiz->questions()->count()]]);
        return back()->with('success',__('Question added to quiz.'));
    }

    public function publish($courseId, $quizId)
    {
        $course = $this->course($courseId);
        $quiz = LmsQuiz::where('course_id',$course->id)->whereKey($quizId)->with('questions.options')->firstOrFail();
        abort_if($quiz->questions->isEmpty(),422,'Add at least one question before publishing.');
        foreach ($quiz->questions as $question) abort_if($question->options->where('is_correct',true)->isEmpty(),422,'Every published question needs a correct answer.');
        $quiz->update(['status'=>'published']);
        return back()->with('success',__('Quiz published.'));
    }

    public function releaseResults($courseId, $quizId, LmsQuizAttempt $attempt, LmsQuizService $service)
    {
        $course = $this->course($courseId);
        $quiz = LmsQuiz::where('course_id',$course->id)->whereKey($quizId)->firstOrFail();
        abort_unless((int)$attempt->lms_quiz_id === (int)$quiz->id,404);
        $service->releaseResult($attempt);
        return back()->with('success',__('Quiz result released to the learner.'));
    }
}
