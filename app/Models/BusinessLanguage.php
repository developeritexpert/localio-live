<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessLanguage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'business_id',
        'language_id',
    ];

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function language(){
        return $this->belongsTo(Language::class ,'language_id');
    }
}
