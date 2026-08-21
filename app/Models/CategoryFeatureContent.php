<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFeatureContent extends Model
{
    use HasFactory;

    protected $table = 'category_feature_contents';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
