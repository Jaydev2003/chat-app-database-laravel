<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ['phone_number', 'otp', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Check if OTP is still valid
     */
    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}
