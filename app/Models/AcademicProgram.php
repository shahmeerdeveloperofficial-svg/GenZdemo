<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicProgram extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = ['university_id', 'country_id', 'study_level_id', 'subject_id', 'legacy_subject_id', 'title', 'slug', 'degree_award', 'description', 'duration', 'tuition_amount', 'tuition_currency', 'entry_requirements', 'source_url', 'last_verified_at', 'status', 'meta_title', 'meta_description', 'created_by'];
    protected $casts = ['entry_requirements' => 'array', 'last_verified_at' => 'datetime', 'tuition_amount' => 'decimal:2'];

    public function university() { return $this->belongsTo(University::class); }
    public function country() { return $this->belongsTo(Country::class); }
    public function studyLevel() { return $this->belongsTo(StudyLevel::class, 'study_level_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function intakes() { return $this->hasMany(ProgramIntake::class, 'academic_program_id')->orderBy('start_date'); }
}
