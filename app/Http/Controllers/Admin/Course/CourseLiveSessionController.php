<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseBatchMembership;
use App\Models\CourseEnrollment;
use App\Models\CourseLiveAttendance;
use App\Models\CourseLiveSession;
use App\Services\CrmTaskNotificationOutbox;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CourseLiveSessionController extends Controller
{
    use ResponseTrait;

    private function course($id): Course
    {
        $query = Course::whereKey((int) $id);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $query->where('created_by',auth()->id());
        return $query->firstOrFail();
    }

    public function store(Request $request, $courseId)
    {
        abort_unless(Schema::hasTable('course_live_sessions'),503,'Apply live class migrations first.');
        $course = $this->course($courseId);
        $data = $request->validate([
            'id'=>['nullable','integer'], 'title'=>['required','string','max:180'], 'description'=>['nullable','string','max:5000'],
            'course_batch_id'=>['nullable','integer',Rule::exists('course_batches','id')->where('course_id',$course->id)->where('status','active')],
            'provider'=>['required',Rule::in(['external','zoom','google_meet'])], 'meeting_url'=>['nullable','url','regex:/^https:\/\//i','max:2000'],
            'recording_url'=>['nullable','url','regex:/^https:\/\//i','max:2000'], 'starts_at'=>['required','date','after:now'],
            'duration_minutes'=>['required','integer','min:10','max:480'], 'timezone'=>['required','timezone'],
        ]);
        $session = !empty($data['id']) ? CourseLiveSession::where('course_id',$course->id)->whereKey($data['id'])->firstOrFail() : new CourseLiveSession();
        $rescheduled = $session->exists && (
            $session->starts_at?->toIso8601String() !== \Illuminate\Support\Carbon::parse($data['starts_at'])->toIso8601String()
            || $session->timezone !== $data['timezone']
            || (int) $session->duration_minutes !== (int) $data['duration_minutes']
            || (int) $session->course_batch_id !== (int) ($data['course_batch_id'] ?? 0)
        );
        $session->course_id = $course->id; $session->course_batch_id = $data['course_batch_id'] ?? null;
        $session->title = $data['title']; $session->description = $data['description'] ?? null; $session->provider = $data['provider'];
        if (array_key_exists('meeting_url',$data)) $session->meeting_url = $data['meeting_url'];
        if (array_key_exists('recording_url',$data)) $session->recording_url = $data['recording_url'];
        $session->starts_at = $data['starts_at']; $session->duration_minutes = $data['duration_minutes']; $session->timezone = $data['timezone'];
        $session->status = 'scheduled'; $session->created_by ??= auth()->id();
        if ($rescheduled) $session->schedule_revision++;
        $session->save();

        if ($rescheduled && Schema::hasTable('crm_task_notification_outbox')) {
            $enrollments = CourseEnrollment::where('course_id',$course->id)->where('status',STATUS_ACTIVE);
            if (Schema::hasColumn('course_enrollments','access_expires_at')) $enrollments->where(fn ($q) => $q->whereNull('access_expires_at')->orWhere('access_expires_at','>',now()));
            if ($session->course_batch_id) $enrollments->whereIn('id',CourseBatchMembership::where('course_batch_id',$session->course_batch_id)->select('course_enrollment_id'));
            foreach ($enrollments->get() as $enrollment) app(CrmTaskNotificationOutbox::class)->enqueue(
                'course-live-rescheduled:'.$session->id.':'.$session->schedule_revision.':'.$enrollment->user_id,
                (int) $enrollment->user_id, __('Live class rescheduled'), $session->title.' · '.$session->starts_at->timezone($session->timezone)->format('M j, Y g:i A T'), route('student.my_courses.view',encodeId($enrollment->id))
            );
        }
        return $this->success([], __('Live class saved. Meeting details are shown only to enrolled learners during the join window.'));
    }

    public function cancel($courseId, $sessionId)
    {
        $course = $this->course($courseId);
        CourseLiveSession::where('course_id',$course->id)->whereKey($sessionId)->firstOrFail()->update(['status'=>'cancelled']);
        return $this->success([], __('Live class cancelled.'));
    }

    public function markAttendance(Request $request, $courseId, $sessionId, $enrollmentId)
    {
        $course = $this->course($courseId);
        $session = CourseLiveSession::where('course_id',$course->id)->whereKey($sessionId)->firstOrFail();
        $enrollment = CourseEnrollment::where('course_id',$course->id)->whereKey($enrollmentId)->where('status',STATUS_ACTIVE)->firstOrFail();
        app(\App\Services\LmsEnrollmentService::class)->activeEnrollment($enrollment->id,(int) $enrollment->user_id);
        if ($session->course_batch_id) abort_unless(CourseBatchMembership::where('course_batch_id',$session->course_batch_id)->where('course_enrollment_id',$enrollment->id)->exists(),404);
        $data = $request->validate(['attendance_status'=>['required',Rule::in(['attended','late','absent'])]]);
        CourseLiveAttendance::updateOrCreate(
            ['course_live_session_id'=>$session->id,'course_enrollment_id'=>$enrollment->id],
            ['user_id'=>$enrollment->user_id,'attendance_status'=>$data['attendance_status'],'joined_at'=>$data['attendance_status']==='absent' ? null : now(),'last_seen_at'=>now()]
        );
        return $this->success([], __('Attendance updated.'));
    }
}
