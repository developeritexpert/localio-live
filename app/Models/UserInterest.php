<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id', 'interest_type', 'interest_id', 
        'score', 'last_updated'
    ];

    protected $dates = ['last_updated'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
