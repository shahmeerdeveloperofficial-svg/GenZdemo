<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsQuestion extends Model
{
    use SoftDeletes;
    protected $fillable = ['course_id','question_bank_id','question_text','question_type','explanation','points','status','created_by'];
    public function options() { return $this->hasMany(LmsQuestionOption::class,'lms_question_id')->orderBy('sort_order'); }
    public function quizzes() { return $this->belongsToMany(LmsQuiz::class,'lms_quiz_questions','lms_question_id','lms_quiz_id'); }
}
