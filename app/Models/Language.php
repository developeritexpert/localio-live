<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $fillable = ['lang_code', 'name', 'base_language_id', 'country_id', 'status', 'is_active_translation', 'is_valid_language_code'];

    public function categoryTranslations()
    {
        return $this->hasMany(CategoryTranslation::class, 'lang_id');
    }
    public function staticContents()
    {
        return $this->hasMany(StaticContentKey::class, 'lang_id');
    }
    public function baseLanguage()
    {
        return $this->belongsTo(Language::class, 'base_language_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_languages')
            ->withTimestamps();
    }

    public function policyTranslations()
    {
        return $this->hasMany(PolicyTranslation::class, 'language_id');
    }
    public function faqTranslations()
    {
        return $this->hasMany(FaqTranslation::class, 'language_id');
    }
}
