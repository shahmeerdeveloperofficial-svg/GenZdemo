<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class University extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'country_id',
        'thumbnail_image',
        'world_ranking',
        'international_student',
        'details',
        'core_benefits_title',
        'core_benefits_icon',
        'gallery_image',
        'avg_cost',
        'city', 'website_url', 'tuition_currency', 'source_url', 'last_verified_at',
        'feature',
        'top_university',
        'status',
    ];
    protected $casts = [
        'core_benefits_title' => 'array',
        'core_benefits_icon' => 'array',
        'gallery_image' => 'array',
        'last_verified_at' => 'datetime',
    ];

    public function country() { return $this->belongsTo(Country::class, 'country_id'); }
    public function academicPrograms() { return $this->hasMany(AcademicProgram::class); }
    public function scholarships() { return $this->hasMany(Scholarship::class); }
}
