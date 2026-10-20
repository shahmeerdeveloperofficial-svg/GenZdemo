<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\FileManager;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Services\LmsAssignmentService;
use App\Services\LmsEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseAssignmentController extends Controller
{
    public function index($enrollmentId, LmsEnrollmentService $enrollments)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $assignments = LmsAssignment::where('course_id',$enrollment->course_id)->where('status','published')->with(['submissions'=>fn($q)=>$q->where('course_enrollment_id',$enrollment->id)->with('grade')->orderByDesc('version')])->orderBy('due_at')->get();
        return view('student.my_courses.assignments',compact('enrollment','assignments'));
    }

    public function submit(Request $request, $enrollmentId, $assignmentId, LmsEnrollmentService $enrollments, LmsAssignmentService $service)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $assignment = LmsAssignment::where('course_id',$enrollment->course_id)->whereKey($assignmentId)->where('status','published')->firstOrFail();
        $data = $request->validate(['text_response'=>['nullable','string','max:50000'],'submission_action'=>['required','in:draft,submit'],'file'=>['nullable','file','max:20480','mimes:pdf,docx,txt,png,jpg,jpeg']]);
        $fileId = null; $storedPath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file'); $name = Str::uuid().'.'.$file->getClientOriginalExtension();
            $storedPath = $file->storeAs('lms/assignments/'.$assignment->id.'/'.$enrollment->user_id,$name,'private');
            $fileId = FileManager::create(['file_type'=>$file->getMimeType(),'storage_type'=>'private','original_name'=>$file->getClientOriginalName(),'file_name'=>$name,'user_id'=>auth()->id(),'path'=>$storedPath,'extension'=>strtolower($file->getClientOriginalExtension()),'size'=>$file->getSize()])->id;
        }
        try {
            $submission = $service->saveSubmission($assignment,$enrollment->id,(int)auth()->id(),$data['text_response']??null,$fileId,$data['submission_action']==='submit');
        } catch (ValidationException $exception) {
            if ($storedPath) Storage::disk('private')->delete($storedPath);
            if ($fileId) FileManager::whereKey($fileId)->delete();
            throw $exception;
        }
        return redirect()->route('student.my_courses.assignments.index',encodeId($enrollment->id))->with('success',$submission->status==='draft'?__('Draft saved.'):__('Assignment submitted.'));
    }

    public function download($enrollmentId, $submissionId, LmsEnrollmentService $enrollments)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int)auth()->id());
        $submission = LmsAssignmentSubmission::where('course_enrollment_id',$enrollment->id)->where('user_id',auth()->id())->whereKey($submissionId)->firstOrFail();
        return $this->privateFile($submission->file_manager_id);
    }

    private function privateFile(?int $fileId)
    {
        $file = FileManager::whereKey($fileId)->where('storage_type','private')->firstOrFail();
        abort_unless((int)$file->user_id === (int)auth()->id(),404);
        abort_unless(Storage::disk('private')->exists($file->path),404);
        return response()->download(Storage::disk('private')->path($file->path),basename($file->original_name),['X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store']);
    }
}
