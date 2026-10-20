<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBatchMembership extends Model
{
    protected $fillable = ['course_batch_id','course_enrollment_id','joined_at'];
    protected $casts = ['joined_at'=>'datetime'];
    public function batch() { return $this->belongsTo(CourseBatch::class, 'course_batch_id'); }
    public function enrollment() { return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id'); }
}
