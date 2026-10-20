<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSavedOption extends Model
{
    public const ITEM_TYPES = ['country', 'university', 'academic_program', 'scholarship'];
    protected $fillable = ['user_id', 'item_type', 'item_id'];
    public function student() { return $this->belongsTo(User::class, 'user_id'); }
}
