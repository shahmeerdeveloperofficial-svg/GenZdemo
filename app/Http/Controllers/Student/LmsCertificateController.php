<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LmsCertificate;
use Illuminate\Support\Facades\Schema;

class LmsCertificateController extends Controller
{
    public function index()
    {
        abort_unless(Schema::hasTable('lms_certificates'),503,'Certificates are not enabled until the LMS certificate migration is applied.');
        $certificates=LmsCertificate::where('user_id',auth()->id())->with('course')->latest('issued_at')->paginate(20);
        return view('student.my_courses.certificates',compact('certificates'));
    }

    public function show($id)
    {
        abort_unless(Schema::hasTable('lms_certificates'),503,'Certificates are not enabled until the LMS certificate migration is applied.');
        $certificate=LmsCertificate::where('user_id',auth()->id())->with(['course','user'])->whereKey($id)->firstOrFail();
        return view('student.my_courses.certificate',compact('certificate'));
    }
}
