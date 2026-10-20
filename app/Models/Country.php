<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'details',
        'banner_image',
        'core_benefits_title',
        'core_benefits_icon',
        'gallery_image', 'country_code', 'currency_code', 'source_url', 'last_verified_at',
        'status'
    ];

    protected $casts = [
        'core_benefits_title' => 'array',
        'core_benefits_icon' => 'array',
        'gallery_image' => 'array',
        'last_verified_at' => 'datetime',
    ];

    public function universities() { return $this->hasMany(University::class); }
    public function academicPrograms() { return $this->hasMany(AcademicProgram::class); }
}
