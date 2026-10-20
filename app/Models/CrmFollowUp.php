<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmFollowUp extends Model
{
    protected $fillable = ['lead_id', 'assigned_to', 'created_by', 'title', 'description', 'due_at', 'completed_at', 'priority', 'status'];
    protected $casts = ['due_at' => 'datetime', 'completed_at' => 'datetime'];
    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
    public function owner() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function comments() { return $this->hasMany(CrmFollowUpComment::class, 'follow_up_id')->with('author')->latest(); }
}
