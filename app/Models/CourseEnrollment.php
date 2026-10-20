<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnrollment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'access_expires_at','expired_at','revoked_at','revocation_reason','enrollment_source','payment_id',
    ];

    protected $casts = ['access_expires_at'=>'datetime','expired_at'=>'datetime','revoked_at'=>'datetime'];

    /**
     * Relationships
     */

    // An enrollment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // An enrollment belongs to a course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function batchMembership() { return $this->hasOne(CourseBatchMembership::class, 'course_enrollment_id'); }
    public function certificate() { return $this->hasOne(LmsCertificate::class, 'course_enrollment_id'); }
}
