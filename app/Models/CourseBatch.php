<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseBatch extends Model
{
    use SoftDeletes;
    protected $fillable = ['course_id','title','starts_at','ends_at','timezone','capacity','status','created_by'];
    protected $casts = ['starts_at'=>'datetime','ends_at'=>'datetime'];
    public function course() { return $this->belongsTo(Course::class); }
    public function memberships() { return $this->hasMany(CourseBatchMembership::class); }
}
