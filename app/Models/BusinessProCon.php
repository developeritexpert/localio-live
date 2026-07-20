<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProCon extends Model
{
    protected $fillable = ['business_id', 'type', 'text'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
