<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatusHistory extends Model
{
    protected $table = 'application_status_history';
    protected $fillable = ['admission_application_id','from_status','to_status','actor_id','reason','metadata'];
    protected $casts = ['metadata' => 'array'];
    public function application() { return $this->belongsTo(AdmissionApplication::class, 'admission_application_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
