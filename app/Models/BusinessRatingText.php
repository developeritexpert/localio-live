<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRatingText extends Model
{
    use HasFactory;

    protected $table = 'business_rating_texts';

    protected $fillable = [
        'business_id',
        'criteria_key',
        'intro_text',
        'end_text',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
