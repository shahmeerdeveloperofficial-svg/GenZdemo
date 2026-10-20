<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsAssignmentSubmission extends Model
{
    protected $fillable = ['lms_assignment_id','course_enrollment_id','user_id','version','status','text_response','file_manager_id','is_late','submitted_at'];
    protected $casts = ['is_late'=>'boolean','submitted_at'=>'datetime'];
    public function assignment() { return $this->belongsTo(LmsAssignment::class,'lms_assignment_id'); }
    public function grade() { return $this->hasOne(LmsGrade::class,'lms_assignment_submission_id'); }
    public function user() { return $this->belongsTo(User::class,'user_id'); }
}
