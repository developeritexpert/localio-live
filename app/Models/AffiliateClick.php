<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'click_id', 'gclid', 'msclkid', 'business_id', 
        'revenue', 'converted', 'clicked_at', 'converted_at'
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'converted_at' => 'datetime',
        'converted' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
