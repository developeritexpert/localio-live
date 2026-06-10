<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterOption extends Model
{
    use HasFactory;
    protected $table = 'product_filter_options';
    protected $fillable = ['category_id', 'filter_id', 'filter_option_id', 'product_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }

    public function filterOption()
    {
        return $this->belongsTo(FilterOption::class,'filter_option_id');
    }
}


