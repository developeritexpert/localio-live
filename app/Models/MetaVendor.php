<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaVendor extends Model
{
    use HasFactory;
    protected $fillable = ['company_name', 'company_size', 'job_title','user_id'];

}
