<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'filter_type_id',
        'is_required',
        'display_order',
       
    ];
    use HasFactory;
    public function translations()
    {
        return $this->hasMany(FilterTranslation::class);
    }
    public function options(){
        return $this->hasMany(FilterOption::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function filterType()
    {
        return $this->belongsTo(FilterType::class);
    }
    public function filterOptions()
    {
        return $this->hasMany(FilterOption::class, 'filter_id', 'id');
    }

}
