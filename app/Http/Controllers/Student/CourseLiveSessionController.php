<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseBatchMembership;
use App\Models\CourseLiveSession;
use App\Services\CourseLiveSessionService;
use App\Services\LmsEnrollmentService;
use Illuminate\Http\Request;

class CourseLiveSessionController extends Controller
{
    public function join($enrollmentId, $sessionId, LmsEnrollmentService $enrollments, CourseLiveSessionService $sessions)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int) auth()->id());
        $session = CourseLiveSession::whereKey($sessionId)->where('course_id',$enrollment->course_id)->where('status','scheduled')->firstOrFail();
        $sessions->join($session,$enrollment);
        if (request()->expectsJson()) return response()->json(['ok'=>true,'meeting_url'=>$session->meeting_url]);
        return redirect()->away($session->meeting_url);
    }

    public function heartbeat(Request $request, $enrollmentId, $sessionId, LmsEnrollmentService $enrollments, CourseLiveSessionService $sessions)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int) auth()->id());
        $session = CourseLiveSession::whereKey($sessionId)->where('course_id',$enrollment->course_id)->where('status','scheduled')->firstOrFail();
        $attendance = $sessions->heartbeat($session,$enrollment);
        return response()->json(['ok'=>true,'attendance_status'=>$attendance->attendance_status]);
    }

    public function recording($enrollmentId, $sessionId, LmsEnrollmentService $enrollments, CourseLiveSessionService $sessions)
    {
        $enrollment = $enrollments->activeEnrollment(decodeId($enrollmentId),(int) auth()->id());
        $session = CourseLiveSession::whereKey($sessionId)->where('course_id',$enrollment->course_id)->firstOrFail();
        return redirect()->away($sessions->recording($session,$enrollment));
    }
}
