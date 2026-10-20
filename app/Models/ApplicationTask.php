<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationTask extends Model
{
    protected $fillable = ['admission_application_id','assignee_id','created_by','title','description','due_at','completed_at'];
    protected $casts = ['due_at' => 'datetime','completed_at' => 'datetime'];
}
