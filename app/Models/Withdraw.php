<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'beneficiary_id',
        'transection_id',
        'amount',
        'payment_method',
        'note',
        'status',
    ];

    protected static function booted()
    {
        self::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', WITHDRAWAL_STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', WITHDRAWAL_STATUS_COMPLETE);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', WITHDRAWAL_STATUS_REJECTED);
    }
}
