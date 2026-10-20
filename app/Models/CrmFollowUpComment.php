<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmFollowUpComment extends Model
{
    protected $fillable = ['follow_up_id','author_id','body'];
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function followUp() { return $this->belongsTo(CrmFollowUp::class, 'follow_up_id'); }
}
