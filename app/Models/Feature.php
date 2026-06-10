<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeatureTransalte;
use App\Models\Business;

class Feature extends Model
{
    use HasFactory;
    protected $table = 'features';

    protected $guarded = [];

    public function translations()
    {
        return  $this->hasMany(FeatureTransalte::class, 'feature_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_feature');
    }
    public function reviews()
    {
        return $this->hasMany(FeatureBusinessReview::class, 'feature_id');
    }
}
