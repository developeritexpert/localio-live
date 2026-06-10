<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryLanguage extends Model
{
    use HasFactory;
    protected $table = 'country_languages';
    protected $fillable = ['country_id ', 'language_id ', 'created_at', 'updated_at','status','id'];
}