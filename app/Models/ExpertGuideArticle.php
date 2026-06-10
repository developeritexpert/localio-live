<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertGuideArticle extends Model
{
    use HasFactory;
    public function contents()
    {
        return $this->hasMany(ExpertGuideArticleContent::class);
    }

    public function articleTranslations()
    {
        return $this->hasMany(ExpertGuideArticleTranslation::class,'expert_guide_article_id');
    }

    public function category(){
        return $this->belongsTo(ExpertGuideCategory::class ,'category_id');
    }

    public function translationForCurrentLang()
    {
        return $this->hasOne(ExpertGuideArticleTranslation::class, 'expert_guide_article_id')
                    ->where('lang_id', session('lang_id', 1));
    }

}
