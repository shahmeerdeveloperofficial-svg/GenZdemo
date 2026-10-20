<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsCertificate extends Model
{
    protected $fillable = ['course_enrollment_id','course_id','user_id','certificate_number','verification_token','issued_at','revoked_at','issued_by','revoked_by','revocation_reason'];
    protected $casts = ['issued_at'=>'datetime','revoked_at'=>'datetime'];
    public function enrollment() { return $this->belongsTo(CourseEnrollment::class,'course_enrollment_id'); }
    public function course() { return $this->belongsTo(Course::class); }
    public function user() { return $this->belongsTo(User::class); }
}
