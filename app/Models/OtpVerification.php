<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;
    protected $table = 'otp_verifications';

    // Define the fillable properties for mass assignment
    protected $fillable = [
        'user_id',
        'otp',
        'otp_type',
        'expires_at',
    ];

    // Optionally, define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
