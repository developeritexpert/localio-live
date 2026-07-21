<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOffering extends Model
{
    protected $fillable = ['business_id', 'headline', 'top_text', 'bottom_text', 'image'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
