<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmLeadActivity extends Model
{
    protected $fillable = ['lead_id', 'actor_id', 'type', 'from_value', 'to_value', 'description', 'metadata'];
    protected $casts = ['metadata' => 'array'];
    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
