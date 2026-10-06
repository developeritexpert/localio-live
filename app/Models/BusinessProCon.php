<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProCon extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'category_pro_con_id',
        'type',
        'text',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function categoryProCon()
    {
        return $this->belongsTo(CategoryProCon::class, 'category_pro_con_id');
    }
}
