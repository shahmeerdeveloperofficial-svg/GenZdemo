<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmLeadNote extends Model
{
    protected $fillable = ['lead_id', 'author_id', 'body', 'is_private'];
    protected $casts = ['is_private' => 'boolean'];
    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
