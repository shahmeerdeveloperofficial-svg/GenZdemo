<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationNote extends Model
{
    protected $fillable = ['admission_application_id','author_id','body','visibility'];
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
