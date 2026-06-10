<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCountrie extends Model
{
    use HasFactory;
    protected $table = 'business_country';
    protected $fillable = ['business_id ','country_id', 'status'];
}
