<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsAnswer extends Model
{
    protected $fillable = ['lms_quiz_attempt_id','lms_question_id','selected_option_ids','awarded_points'];
    protected $casts = ['selected_option_ids'=>'array'];
}
