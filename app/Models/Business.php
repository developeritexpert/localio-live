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
        'screenshot_urls',
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
        'admin_rating',
    ];
    protected $casts = [
        'is_affiliate' => 'integer',
        'admin_rating' => 'float',
        'business_images' => 'array',
        'screenshot_urls' => 'array',
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
    public function subCategories()
    {
        return $this->belongsToMany(Category::class, 'business_sub_category', 'business_id', 'category_id');
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


    public function getEffectiveWebsiteUrl($countryId = null)
    {
        $countryId = $countryId ?: (function_exists('getCurrentCountry') ? getCurrentCountry() : null);

        if ($countryId) {
            $countryWebsite = $this->relationLoaded('websites')
                ? $this->websites->firstWhere('country_id', $countryId)
                : $this->websites()->where('country_id', $countryId)->first();

            if ($countryWebsite && !empty($countryWebsite->website_url)) {
                return $countryWebsite->website_url;
            }
        }

        return $this->affiliate_link ?: $this->permanent_url;
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

    public function isAffiliated()
    {
        return (bool) $this->is_affiliate;
    }

    /**
     * Get active (approved) user reviews count.
     */
    public function getApprovedReviewsCountAttribute()
    {
        return $this->reviews->where('status', 'active')->count();
    }

    /**
     * Get active (approved) user reviews average rating.
     */
    public function getApprovedUserRatingAttribute()
    {
        $approvedReviews = $this->reviews->where('status', 'active');
        return $approvedReviews->count() > 0 ? round($approvedReviews->avg('rating'), 1) : null;
    }

    /**
     * Get effective business display rating.
     * Uses approved user review rating if available; falls back to admin_rating if set; otherwise null.
     */
    public function getDisplayRatingAttribute()
    {
        $approvedCount = $this->reviews->where('status', 'active')->count();
        if ($approvedCount > 0) {
            return round($this->reviews->where('status', 'active')->avg('rating'), 1);
        }
        if ($this->admin_rating !== null && (float)$this->admin_rating > 0) {
            return (float)$this->admin_rating;
        }
        return null;
    }

    /**
     * Check if business has an uploaded custom logo.
     */
    public function hasLogo()
    {
        $raw = $this->attributes['icon_id'] ?? null;
        if (empty($raw)) {
            return false;
        }
        $defaults = [
            'front/img/default_business_logo.svg',
            'front/img/logo.svg',
            'front/img/default.png',
            'images/default.png',
            'front/img/top-rate-img2.svg',
            'front/img/big-asana.png',
            'front/img/sftare-img1.svg',
            'front/img/poplr-zero.svg',
            'front/img/lyt-rd-grey.svg'
        ];
        return !in_array($raw, $defaults, true);
    }

    public function getHasLogoAttribute()
    {
        return $this->hasLogo();
    }

    /**
     * Get first initial of the business name in uppercase.
     */
    public function getInitialAttribute()
    {
        $name = '';
        if ($this->relationLoaded('translations') && $this->translations->isNotEmpty()) {
            $name = $this->translations->first()->name ?? '';
        } elseif (method_exists($this, 'translations') && $this->translations()->exists()) {
            $name = $this->translations()->first()?->name ?? '';
        }
        if (empty($name) && !empty($this->name)) {
            $name = $this->name;
        }
        $name = trim($name);
        return $name !== '' ? mb_strtoupper(mb_substr($name, 0, 1)) : 'B';
    }

    /**
     * Get business icon/logo with fallback to null if not set.
     */
    public function getIconIdAttribute($value)
    {
        if (!empty($value) && !in_array($value, [
            'front/img/default_business_logo.svg',
            'front/img/logo.svg',
            'front/img/default.png',
            'images/default.png'
        ], true)) {
            return $value;
        }
        return null;
    }

    /**
     * Check if business has approved user reviews (for showing detailed rating bars).
     */
    public function getHasUserReviewsAttribute()
    {
        return $this->reviews->where('status', 'active')->count() > 0;
    }

}

