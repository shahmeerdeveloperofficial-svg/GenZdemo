<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class Course extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'program_id',
        'title',
        'subtitle',
        'slug',
        'thumbnail',
        'intro_video_type',
        'intro_video',
        'duration',
        'price',
        'course_benefits',
        'start_date',
        'description',
        'description_point',
        'faqs',
        'instructors',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'created_by',
        'status',
        'publication_state','difficulty','language','prerequisites','estimated_minutes','capacity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'intro_video_type' => 'integer',
        'instructors' => 'array',
        'description_point' => 'array',
        'course_benefits' => 'array',
        'faqs' => 'array',
        'prerequisites' => 'array',
    ];

    /**
     * Relationships
     */

    // Assuming a course belongs to a category
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_id')->orderBy('sort_order')->orderBy('id');
    }

    // Assuming a course was created by a user
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrolls()
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id');
    }

    public function batches() { return $this->hasMany(CourseBatch::class, 'course_id')->orderByDesc('starts_at'); }

    public function payments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    /** Public course pages and discovery surfaces must never expose drafts or review submissions. */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        $query->where('status', STATUS_ACTIVE);
        if (Schema::hasColumn('courses', 'publication_state')) {
            $query->where('publication_state', 'published');
        }

        return $query;
    }
}
