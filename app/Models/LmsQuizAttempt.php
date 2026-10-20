<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizAttempt extends Model
{
    protected $fillable = ['lms_quiz_id','course_enrollment_id','attempt_number','status','question_order','started_at','deadline_at','submitted_at','score','total_points','percentage','passed','result_released_at'];
    protected $casts = ['question_order'=>'array','started_at'=>'datetime','deadline_at'=>'datetime','submitted_at'=>'datetime','result_released_at'=>'datetime','passed'=>'boolean'];
    public function quiz() { return $this->belongsTo(LmsQuiz::class,'lms_quiz_id'); }
    public function answers() { return $this->hasMany(LmsAnswer::class,'lms_quiz_attempt_id'); }
}
