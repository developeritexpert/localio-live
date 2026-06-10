<?php

namespace App\Models;
use App\Models\ExpertGuideCategoryTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertGuideCategory extends Model
{
    use HasFactory;

    public function expertGuideCategoryTranslation(){
        return $this->hasMany(ExpertGuideCategoryTranslation::class, 'expert_guide_category_id');
    }

    public function translations()
{
    return $this->hasMany(ExpertGuideCategoryTranslation::class, 'expert_guide_category_id');
}
    public function translationForCurrentLang()
    {
        return $this->hasOne(ExpertGuideCategoryTranslation::class, 'expert_guide_category_id')
                    ->where('lang_id', getCurrentLanguageID());
    }

    public function articles(){
        return $this->hasMany(ExpertGuideArticle::class, 'category_id');
    }


}
