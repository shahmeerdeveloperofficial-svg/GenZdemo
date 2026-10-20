<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class StudyAbroadRegistration extends Model
{
    protected $fillable = [
        'lead_id', 'full_name', 'father_name', 'identity_number', 'date_of_birth', 'gender', 'marital_status',
        'phone', 'whatsapp', 'email', 'residential_address', 'last_qualification', 'field_of_study',
        'institute_name', 'passing_year', 'grade', 'destination', 'branch', 'program_type', 'paid_amount',
        'paid_currency', 'signature_path', 'signed_at', 'terms_accepted', 'status',
    ];

    protected $casts = ['date_of_birth' => 'date', 'signed_at' => 'date', 'terms_accepted' => 'boolean', 'paid_amount' => 'decimal:2'];

    public function setIdentityNumberAttribute($value): void
    {
        $this->attributes['identity_number'] = $value ? Crypt::encryptString((string) $value) : '';
    }

    public function getIdentityNumberAttribute($value): ?string
    {
        if (!$value) return null;
        try { return Crypt::decryptString($value); } catch (\Throwable $exception) { return null; }
    }

    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
}
