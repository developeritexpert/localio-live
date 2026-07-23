<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
        'affiliate_partner',
        'meta_title',
        'status',
        'meta_description',
        'category_id',
        'permanent_url',
        'affiliate_link',
        'active_all_countries',
        'icon_id',
        'business_images',
        'image_id',
        'year_found',
        'languages_supported',
        'created_by',
        'is_affiliate',
        'primary_keywords',
        'secondary_keywords',
        'long_tail_keywords',
        'high_intent_keywords',
        'pro_cons_intro',
        'pro_cons_summary',
    ];
    protected $casts = [
        'is_affiliate' => 'integer',

        'business_images' => 'array',

    ];

    protected $table = 'businesses';
    protected $lang_code, $lang_id;

    // Many-to-Many Relationship with Countries
    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }
    public function supportedLanguages()
    {
        return $this->belongsToMany(Language::class, 'business_languages')
            ->withTimestamps();
    }
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'business_languages');
    }
    public function isAvailableInLanguage($langId)
    {
        return $this->languages()->where('language_id', $langId)->exists();
    }

    public function isAvailableInCountry($countryId)
    {
        return $this->countries()->where('country_id', $countryId)->exists();
    }
   // One-to-Many Relationship with BusinessTranslations
   public function translations()
   {
       return $this->hasMany(BusinessTranslation::class, 'business_id');
   }

    // One-to-Many Relationship with BusinessWebsites
    public function websites()
    {
        return $this->hasMany(BusinessWebsite::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'business_product');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'business_id');
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function pricingOptions()
    {
        return $this->belongsToMany(PricingOption::class, 'business_pricing_option');
    }
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'business_feature');
    }
    public function proCons()
    {
        return $this->hasMany(BusinessProCon::class);
    }
    public function offerings()
    {
        return $this->hasMany(BusinessOffering::class);
    }
    public function limitedFeatures()
{
    return $this->features()->limit(4);
}

    public function categoryTopic(){
        return $this->belongsToMany(BusinessCategoryTopic::class);
    }

    // In Business.php
    public function getCategoryTopicsAttribute()
    {
        return $this->category?->categoryTopics;
    }

    // In Business.php
    public function topicDescriptions()
    {
        return $this->hasMany(BusinessTopicDescription::class);
    }


    public function getTrackedUrl()
    {
        return \App\Services\AffiliateTrackingService::trackClick($this);
    }


    public function faqs()
    {
        return $this->hasMany(BusinessFaq::class,'business_id')->active()->ordered();
    }

    public function allFaqs()
    {
        return $this->hasMany(BusinessFaq::class)->ordered();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'business_id');
    }

    public function integration()
    {
        return $this->hasOne(BusinessIntegration::class);
    }

    public function usps()
    {
        return $this->hasMany(BusinessUsp::class)->orderBy('sort_order');
    }

}
