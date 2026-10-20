<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use SoftDeletes;

    public const STATUSES = ['new', 'contacted', 'qualified', 'consultation', 'converted', 'lost'];

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'status', 'source', 'source_detail',
        'assigned_to', 'converted_user_id', 'lost_reason', 'tags', 'preferences',
        'first_contacted_at', 'qualified_at', 'consultation_at', 'converted_at', 'lost_at', 'created_by',
    ];

    protected $casts = ['tags' => 'array', 'preferences' => 'array', 'first_contacted_at' => 'datetime',
        'qualified_at' => 'datetime', 'consultation_at' => 'datetime', 'converted_at' => 'datetime', 'lost_at' => 'datetime'];

    public function activities() { return $this->hasMany(CrmLeadActivity::class, 'lead_id')->orderByDesc('id'); }
    public function notes() { return $this->hasMany(CrmLeadNote::class, 'lead_id')->latest(); }
    public function followUps() { return $this->hasMany(CrmFollowUp::class, 'lead_id')->orderBy('due_at'); }
    public function studyRegistrations() { return $this->hasMany(StudyAbroadRegistration::class, 'lead_id')->latest(); }
    public function owner() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
