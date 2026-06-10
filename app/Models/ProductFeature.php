<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeatureTransalte;

class ProductFeature extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'feature_id', 'feature_type'];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function featureTranslate()
    {
        return $this->belongsTo(FeatureTransalte::class, 'feature_id', 'feature_id');
    }

    public function feature_translation(){
        return $this->belongsTo(FeatureTransalte::class, 'feature_id', 'feature_id');
    }
}
