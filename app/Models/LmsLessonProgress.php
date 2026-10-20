<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLessonProgress extends Model
{
    protected $table = 'lms_lesson_progress';
    protected $fillable = ['course_enrollment_id','course_resource_id','user_id','last_position_seconds','time_spent_seconds','completed_at'];
    protected $casts = ['completed_at'=>'datetime'];
}
