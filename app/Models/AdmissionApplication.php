<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionApplication extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft','document_collection','internal_review','ready_to_submit','submitted','university_review','conditional_offer','unconditional_offer','rejected','withdrawn','accepted','enrolled'];
    public const ACTIVE_STATUSES = ['draft','document_collection','internal_review','ready_to_submit','submitted','university_review','conditional_offer','unconditional_offer','accepted'];
    public const VISA_STATUSES = ['not_started','documents','submitted','approved','refused','withdrawn'];
    protected $fillable = ['reference_code','student_id','university_id','academic_program_id','program_intake_id','counselor_id','application_deadline','status','visa_status','submitted_at','closed_at'];
    protected $casts = ['application_deadline' => 'date', 'submitted_at' => 'datetime', 'closed_at' => 'datetime'];
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function university() { return $this->belongsTo(University::class); }
    public function program() { return $this->belongsTo(AcademicProgram::class, 'academic_program_id'); }
    public function intake() { return $this->belongsTo(ProgramIntake::class, 'program_intake_id'); }
    public function history() { return $this->hasMany(ApplicationStatusHistory::class, 'admission_application_id')->latest(); }
    public function documents() { return $this->hasMany(ApplicationDocument::class, 'admission_application_id'); }
    public function notes() { return $this->hasMany(ApplicationNote::class, 'admission_application_id')->latest(); }
    public function tasks() { return $this->hasMany(ApplicationTask::class, 'admission_application_id')->latest(); }
}
