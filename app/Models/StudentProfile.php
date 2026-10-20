<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class StudentProfile extends Model
{
    protected $fillable = ['user_id', 'date_of_birth', 'gender', 'nationality', 'passport_number', 'present_address', 'permanent_address', 'preferred_country_ids', 'preferred_university_ids', 'preferred_subject_ids', 'target_intake'];
    protected $casts = ['date_of_birth' => 'date', 'preferred_country_ids' => 'array', 'preferred_university_ids' => 'array', 'preferred_subject_ids' => 'array'];

    public function setPassportNumberAttribute($value): void
    {
        $this->attributes['passport_number'] = $value ? Crypt::encryptString((string) $value) : null;
    }

    public function getPassportNumberAttribute($value): ?string
    {
        if (!$value) return null;
        try { return Crypt::decryptString($value); } catch (\Throwable $exception) { return null; }
    }

    public function student() { return $this->belongsTo(User::class, 'user_id'); }
}
