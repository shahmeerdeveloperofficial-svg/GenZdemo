<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLiveSession extends Model
{
    use SoftDeletes;
    protected $fillable = ['course_id','course_batch_id','title','description','provider','meeting_url','recording_url','starts_at','duration_minutes','timezone','status','schedule_revision','created_by'];
    protected $casts = ['meeting_url'=>'encrypted','recording_url'=>'encrypted','starts_at'=>'datetime'];
    public function course() { return $this->belongsTo(Course::class); }
    public function batch() { return $this->belongsTo(CourseBatch::class,'course_batch_id'); }
    public function attendance() { return $this->hasMany(CourseLiveAttendance::class); }
}
