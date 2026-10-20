<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'details',
        'banner_image',
        'university_id',
        'country_id',
        'subject_id',
        'study_level_id',
        'funding_type',
        'application_start_date',
        'application_end_date',
        'available_award_number',
        'offers_received_from_date', 'eligibility_conditions', 'source_url', 'last_verified_at'
    ];

    protected $casts = ['last_verified_at' => 'datetime'];
    public function country() { return $this->belongsTo(Country::class); }
    public function university() { return $this->belongsTo(University::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
}
