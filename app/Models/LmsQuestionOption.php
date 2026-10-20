<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuestionOption extends Model
{
    protected $fillable = ['lms_question_id','option_text','is_correct','sort_order'];
    protected $casts = ['is_correct'=>'boolean'];
}
