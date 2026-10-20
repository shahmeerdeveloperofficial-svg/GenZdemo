<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FileManager;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Services\LmsAssignmentService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseAssignmentController extends Controller
{
    use ResponseTrait;
    private function course($id): Course
    {
        $query=Course::whereKey((int)$id);
        if ((int)auth()->user()->role===USER_ROLE_INSTRUCTOR) $query->where('created_by',auth()->id());
        return $query->firstOrFail();
    }

    public function index($courseId)
    {
        $course=$this->course($courseId);
        $assignments=LmsAssignment::where('course_id',$course->id)->with(['submissions.user','submissions.grade'])->orderByDesc('id')->get();
        return view('admin.courses.assignments.index',compact('course','assignments'));
    }

    public function store(Request $request, $courseId)
    {
        $course=$this->course($courseId);
        $data=$request->validate(['title'=>['required','string','max:180'],'instructions'=>['nullable','string','max:10000'],'due_at'=>['nullable','date'],'max_points'=>['required','integer','min:1','max:10000'],'max_submissions'=>['required','integer','min:1','max:10'],'late_policy'=>['required',Rule::in(['accept','reject'])],'late_penalty_percent'=>['required','integer','min:0','max:100']]);
        LmsAssignment::create(['course_id'=>$course->id,...$data,'status'=>'draft','created_by'=>auth()->id()]);
        return back()->with('success',__('Assignment saved as draft.'));
    }

    public function publish($courseId,$assignmentId)
    {
        $course=$this->course($courseId);
        LmsAssignment::where('course_id',$course->id)->whereKey($assignmentId)->firstOrFail()->update(['status'=>'published']);
        return back()->with('success',__('Assignment published.'));
    }

    public function grade(Request $request,$courseId,$assignmentId,$submissionId,LmsAssignmentService $service)
    {
        $course=$this->course($courseId);
        $assignment=LmsAssignment::where('course_id',$course->id)->whereKey($assignmentId)->firstOrFail();
        $submission=LmsAssignmentSubmission::where('lms_assignment_id',$assignment->id)->whereKey($submissionId)->firstOrFail();
        $data=$request->validate(['raw_score'=>['required','numeric','min:0','max:'.$assignment->max_points],'feedback'=>['nullable','string','max:10000'],'rubric'=>['nullable','array','max:30'],'rubric.*.criterion'=>['required','string','max:180'],'rubric.*.score'=>['required','numeric','min:0'],'release'=>['nullable','boolean']]);
        if (isset($data['rubric']) && collect($data['rubric'])->sum(fn($criterion)=>(float)$criterion['score']) > (float)$data['raw_score']) return back()->withInput()->withErrors(['rubric'=>__('Rubric points cannot exceed the entered score.')]);
        $service->grade($submission,(int)auth()->id(),(float)$data['raw_score'],$data['rubric']??null,$data['feedback']??null,(bool)($data['release']??false));
        return back()->with('success',__('Grade saved.'));
    }

    public function download($courseId,$assignmentId,$submissionId)
    {
        $course=$this->course($courseId);
        $assignment=LmsAssignment::where('course_id',$course->id)->whereKey($assignmentId)->firstOrFail();
        $submission=LmsAssignmentSubmission::where('lms_assignment_id',$assignment->id)->whereKey($submissionId)->firstOrFail();
        $file=FileManager::whereKey($submission->file_manager_id)->where('storage_type','private')->where('user_id',$submission->user_id)->firstOrFail();
        abort_unless(Storage::disk('private')->exists($file->path),404);
        return response()->download(Storage::disk('private')->path($file->path),basename($file->original_name),['X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store']);
    }
}
