<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterType extends Model
{ protected $fillable = ['name', 'slug'];
    use HasFactory;
    public function filters()
    {
        return $this->hasMany(Filter::class);
    }
    public function filterOptions()
{
    return $this->hasMany(FilterOption::class);
}
}
