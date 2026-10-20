<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Beneficiary extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'beneficiary_name',
        'type',
        'card_number',
        'card_holder_name',
        'expire_month',
        'expire_year',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'bank_routing_number',
        'paypal_email',
        'status',
    ];

    protected static function booted()
    {
        self::creating(function ($model){
            $model->uuid = Str::uuid()->toString();
            // Only set user_id if not already set (fallback)
            if (!$model->user_id && auth()->check()) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }
}