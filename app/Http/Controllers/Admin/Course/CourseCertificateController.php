<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LmsCertificate;
use App\Services\LmsCertificateService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

class CourseCertificateController extends Controller
{
    private function course($id): Course
    {
        $query=Course::whereKey((int)$id);
        if ((int)auth()->user()->role===USER_ROLE_INSTRUCTOR) $query->where('created_by',auth()->id());
        return $query->firstOrFail();
    }

    public function index($courseId, LmsCertificateService $service)
    {
        abort_unless(Schema::hasTable('lms_certificates'),503,'Apply the LMS certificate migration before managing certificates.');
        $course=$this->course($courseId);
        $enrollments=CourseEnrollment::where('course_id',$course->id)->where('status',STATUS_ACTIVE)->with(['user','certificate'])->latest('id')->paginate(25);
        $eligibility=[];
        foreach ($enrollments as $enrollment) $eligibility[$enrollment->id]=$service->eligibility($enrollment);
        return view('admin.courses.certificates.index',compact('course','enrollments','eligibility'));
    }

    public function issue($courseId,$enrollmentId,LmsCertificateService $service)
    {
        abort_unless(Schema::hasTable('lms_certificates'),503,'Apply the LMS certificate migration before issuing certificates.');
        $course=$this->course($courseId);
        $enrollment=CourseEnrollment::where('course_id',$course->id)->whereKey($enrollmentId)->firstOrFail();
        $service->issue($enrollment,(int)auth()->id());
        return back()->with('success',__('Certificate issued or already exists.'));
    }

    public function revoke(Request $request,$courseId,$certificateId,LmsCertificateService $service)
    {
        abort_unless(Schema::hasTable('lms_certificates'),503,'Apply the LMS certificate migration before revoking certificates.');
        $course=$this->course($courseId);
        $data=$request->validate(['reason'=>['required','string','max:500']]);
        $certificate=LmsCertificate::where('course_id',$course->id)->whereKey($certificateId)->firstOrFail();
        $service->revoke($certificate,(int)auth()->id(),$data['reason']);
        return back()->with('success',__('Certificate revoked.'));
    }
}
