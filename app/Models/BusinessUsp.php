<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessUsp extends Model
{
    use HasFactory;

    protected $table = 'business_usps';

    protected $fillable = [
        'business_id',
        'text',
        'sort_order',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
