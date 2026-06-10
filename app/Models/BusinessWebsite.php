<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessWebsite extends Model
{
    use HasFactory;
    protected $table = 'business_websites';
    protected $fillable = ['business_id','country_id','is_affiliate',
    'website_url',
    'status'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
