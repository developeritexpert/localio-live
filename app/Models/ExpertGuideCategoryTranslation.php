<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertGuideCategoryTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['lang_id', 'name', 'slug', 'description', 'status'];

    public function expertGuideCategory()
    {
        return $this->belongsTo(ExpertGuideCategory::class, 'expert_guide_category_id');
    }
   
}
