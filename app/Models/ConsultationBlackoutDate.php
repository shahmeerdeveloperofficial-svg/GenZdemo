<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationBlackoutDate extends Model
{
    protected $fillable = ['consultant_id','date','reason','created_by'];
    protected $casts = ['date' => 'date'];
    public function consultant() { return $this->belongsTo(User::class, 'consultant_id'); }
}
