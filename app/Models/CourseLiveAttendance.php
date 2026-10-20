<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLiveAttendance extends Model
{
    protected $table = 'course_live_attendance';
    protected $fillable = ['course_live_session_id','course_enrollment_id','user_id','joined_at','last_seen_at','left_at','attended_seconds','attendance_status'];
    protected $casts = ['joined_at'=>'datetime','last_seen_at'=>'datetime','left_at'=>'datetime'];
    public function session() { return $this->belongsTo(CourseLiveSession::class,'course_live_session_id'); }
    public function enrollment() { return $this->belongsTo(CourseEnrollment::class,'course_enrollment_id'); }
}
