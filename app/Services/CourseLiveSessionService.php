<?php

namespace App\Services;

use App\Models\CourseBatchMembership;
use App\Models\CourseEnrollment;
use App\Models\CourseLiveAttendance;
use App\Models\CourseLiveSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CourseLiveSessionService
{
    public function join(CourseLiveSession $session, CourseEnrollment $enrollment): CourseLiveAttendance
    {
        return DB::transaction(function () use ($session,$enrollment) {
            $locked = CourseLiveSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $locked->course_id === (int) $enrollment->course_id && $locked->status === 'scheduled', 404);
            if ($locked->course_batch_id && !CourseBatchMembership::where('course_batch_id',$locked->course_batch_id)->where('course_enrollment_id',$enrollment->id)->exists()) abort(404);
            $starts = $locked->starts_at->copy()->timezone($locked->timezone);
            $ends = $starts->copy()->addMinutes($locked->duration_minutes);
            $now = now($locked->timezone);
            if ($now->lt($starts->copy()->subMinutes(15)) || $now->gt($ends)) throw ValidationException::withMessages(['session'=>__('The live class is not currently open for joining.')]);
            $attendance = CourseLiveAttendance::firstOrCreate(
                ['course_live_session_id'=>$locked->id,'course_enrollment_id'=>$enrollment->id],
                ['user_id'=>$enrollment->user_id,'joined_at'=>now(),'last_seen_at'=>now(),'attendance_status'=>'joined']
            );
            if (!$locked->meeting_url) throw ValidationException::withMessages(['session'=>__('The meeting link has not been configured yet.')]);
            return $attendance;
        });
    }

    public function heartbeat(CourseLiveSession $session, CourseEnrollment $enrollment): CourseLiveAttendance
    {
        $attendance = CourseLiveAttendance::where('course_live_session_id',$session->id)->where('course_enrollment_id',$enrollment->id)->firstOrFail();
        $ends = $session->starts_at->copy()->timezone($session->timezone)->addMinutes($session->duration_minutes);
        abort_if(now($session->timezone)->gt($ends), 409, 'This session has ended.');
        return DB::transaction(function () use ($attendance,$session) {
            $row = CourseLiveAttendance::whereKey($attendance->id)->lockForUpdate()->firstOrFail();
            $now = now();
            $anchor = $row->last_seen_at && $row->last_seen_at->gt($session->starts_at) ? $row->last_seen_at : $session->starts_at;
            $elapsed = $now->lt($session->starts_at) ? 0 : max(0,min(90,$anchor->diffInSeconds($now)));
            $row->attended_seconds = min($session->duration_minutes * 60, $row->attended_seconds + $elapsed);
            $row->last_seen_at = now();
            $threshold = min($session->duration_minutes * 60, max(300,(int) ceil($session->duration_minutes * 30)));
            if ($row->attended_seconds >= $threshold) $row->attendance_status = 'attended';
            $row->save();
            return $row;
        });
    }

    public function recording(CourseLiveSession $session, CourseEnrollment $enrollment): string
    {
        abort_unless((int) $session->course_id === (int) $enrollment->course_id,404);
        if ($session->course_batch_id) abort_unless(CourseBatchMembership::where('course_batch_id',$session->course_batch_id)->where('course_enrollment_id',$enrollment->id)->exists(),404);
        abort_unless($session->starts_at->copy()->addMinutes($session->duration_minutes)->isPast() && $session->recording_url,404);
        return $session->recording_url;
    }
}
