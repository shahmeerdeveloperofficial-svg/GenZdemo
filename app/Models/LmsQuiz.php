<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsQuiz extends Model
{
    use SoftDeletes;
    protected $fillable = ['course_id','course_lesson_id','title','description','status','opens_at','closes_at','time_limit_seconds','max_attempts','pass_percent','shuffle_questions','manual_result_release','created_by'];
    protected $casts = ['opens_at'=>'datetime','closes_at'=>'datetime','shuffle_questions'=>'boolean','manual_result_release'=>'boolean'];
    public function questions() { return $this->belongsToMany(LmsQuestion::class,'lms_quiz_questions','lms_quiz_id','lms_question_id')->withPivot('sort_order')->orderBy('lms_quiz_questions.sort_order'); }
    public function attempts() { return $this->hasMany(LmsQuizAttempt::class,'lms_quiz_id'); }
}
