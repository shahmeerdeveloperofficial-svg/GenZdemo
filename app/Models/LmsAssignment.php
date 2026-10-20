<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsAssignment extends Model
{
    use SoftDeletes;
    protected $fillable = ['course_id','course_lesson_id','title','instructions','due_at','max_points','max_submissions','late_policy','late_penalty_percent','status','created_by'];
    protected $casts = ['due_at'=>'datetime'];
    public function submissions() { return $this->hasMany(LmsAssignmentSubmission::class,'lms_assignment_id'); }
}
