<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramIntake extends Model
{
    protected $fillable = ['academic_program_id', 'label', 'start_date', 'application_deadline', 'status', 'source_url', 'last_verified_at'];
    protected $casts = ['start_date' => 'date', 'application_deadline' => 'date', 'last_verified_at' => 'datetime'];
    public function program() { return $this->belongsTo(AcademicProgram::class, 'academic_program_id'); }
}
