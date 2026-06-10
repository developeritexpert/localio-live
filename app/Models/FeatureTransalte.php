<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Feature;

class FeatureTransalte extends Model
{
    protected $table = 'feature_translations';

    protected $guarded = [];

    use HasFactory;
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
