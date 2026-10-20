<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsGrade extends Model
{
    protected $fillable = ['lms_assignment_submission_id','grader_id','raw_score','score','max_points','rubric','feedback','released_at'];
    protected $casts = ['rubric'=>'array','released_at'=>'datetime'];
    public function submission() { return $this->belongsTo(LmsAssignmentSubmission::class,'lms_assignment_submission_id'); }
}
