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
        'screenshot_categories',
        'screenshot_display_category',
        'is_claimed',
        'website',
    ];
    protected $casts = [
        'is_affiliate' => 'integer',
        'admin_rating' => 'float',
        'business_images' => 'array',
        'screenshot_urls' => 'array',
        'screenshot_categories' => 'array',
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

    public function offerProducts()
    {
        return $this->hasMany('App\\Models\\BusinessOfferProduct', 'business_id')->orderBy('sort_order', 'asc');
    }

    public function hasOfferPlans(): bool
    {
        return $this->offerProducts()->where('has_plans', true)->whereHas('plans')->exists();
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

            if ($countryWebsite) {
                // If country explicitly has affiliate disabled (0), it is non-affiliated for this country
                if (isset($countryWebsite->is_affiliate) && !$countryWebsite->is_affiliate) {
                    return null;
                }
                if (!empty($countryWebsite->website_url)) {
                    return $countryWebsite->website_url;
                }
            }
        }

        if ($this->is_affiliate || !empty($this->affiliate_link)) {
            return $this->affiliate_link ?: $this->permanent_url;
        }

        return $this->permanent_url ?: $this->affiliate_link;
    }

    public function getTrackedUrl($locale = null)
    {
        $loc = $locale ?: (session('lang_code') ?: (request()->route('locale') ?: (request()->segment(1) ?: 'en-us')));

        $countryId = null;
        if (function_exists('getCurrentCountry')) {
            $countryId = getCurrentCountry();
        }

        $targetUrl = $this->getEffectiveWebsiteUrl($countryId);
        if (!$targetUrl) {
            return null;
        }

        $slug = $this->translation->slug ?? ($this->translations->first()->slug ?? \Illuminate\Support\Str::slug($this->name));

        return url('/' . strtolower($loc) . '/go/' . $slug);
    }

    public function isAffiliatedForCountry($countryId = null)
    {
        $countryId = $countryId ?: (function_exists('getCurrentCountry') ? getCurrentCountry() : null);

        if ($countryId) {
            $countryWebsite = $this->relationLoaded('websites')
                ? $this->websites->firstWhere('country_id', $countryId)
                : $this->websites()->where('country_id', $countryId)->first();

            if ($countryWebsite) {
                return !empty($countryWebsite->is_affiliate);
            }
        }

        return !empty($this->is_affiliate) || !empty($this->affiliate_link);
    }


    public function faqs()
    {
        return $this->hasMany(BusinessFaq::class,'business_id')->active()->ordered();
    }
    public function discussions()
    {
        return $this->hasMany(BusinessDiscussion::class, 'business_id')
        ->where('status', 'active')
        ->orderBy('is_pinned', 'desc')
        ->latest();
    }

    public function faqCategories()
    {
        return $this->hasMany(BusinessFaqCategory::class, 'business_id')->active()->ordered();
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

    public function getIsAffiliateAttribute($value)
    {
        if (!empty($value)) {
            return 1;
        }
        if (!empty($this->affiliate_link)) {
            return 1;
        }
        if ($this->relationLoaded('websites') && $this->websites->contains(fn($w) => !empty($w->is_affiliate))) {
            return 1;
        }
        if ($this->exists && method_exists($this, 'websites') && $this->websites()->where('is_affiliate', 1)->exists()) {
            return 1;
        }
        return 0;
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
        if (isset($this->attributes['approved_reviews_count'])) {
            return (int) $this->attributes['approved_reviews_count'];
        }
        if (isset($this->attributes['active_reviews_count'])) {
            return (int) $this->attributes['active_reviews_count'];
        }
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->whereIn('status', ['published', 'active'])->count();
        }
        return $this->reviews()->whereIn('status', ['published', 'active'])->count();
    }

    /**
     * Get active (approved) user reviews average rating.
     */
    public function getApprovedUserRatingAttribute()
    {
        $approvedReviews = $this->relationLoaded('reviews')
            ? $this->reviews->whereIn('status', ['published', 'active'])
            : $this->reviews()->whereIn('status', ['published', 'active']);
        return $approvedReviews->count() > 0 ? round($approvedReviews->avg('rating'), 1) : null;
    }



    /**
     * Get effective business display rating.
     * When business has >= 10 reviews: uses user reviews average.
     * When business has < 10 reviews: uses admin_rating if set, otherwise null.
     */
    public function getDisplayRatingAttribute(): ?float
    {
        $approvedCount = $this->approved_reviews_count;
        if ($approvedCount >= 10) {
            $avg = $this->relationLoaded('reviews')
                ? $this->reviews->whereIn('status', ['published', 'active'])->avg('rating')
                : $this->reviews()->whereIn('status', ['published', 'active'])->avg('rating');
            return $avg !== null ? round((float) $avg, 1) : null;
        }
        if ($this->admin_rating !== null && (float)$this->admin_rating > 0) {
            return round((float)$this->admin_rating, 1);
        }
        return null;
    }

    /**
     * Whether rating stars and number should be shown.
     */
    public function getShouldShowRatingAttribute(): bool
    {
        return $this->display_rating !== null && (float)$this->display_rating > 0;
    }

    /**
     * Whether review count (e.g. (5)) should be shown.
     * Never shown when approved reviews < 10.
     */
    public function getShouldShowReviewCountAttribute(): bool
    {
        return $this->approved_reviews_count >= 10;
    }

    /**
     * Whether detailed user ratings (criteria bars & recommend %) should be shown.
     * Never shown when approved reviews < 10.
     */
    public function getShouldShowDetailedRatingsAttribute(): bool
    {
        return $this->approved_reviews_count >= 10;
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
            'front/img/lyt-rd-grey.svg',
            'front/img/slider1_img.svg',
            'front/img/slider2_img.svg',
            'front/img/slider3_img.svg',
            'business_icon/1746103824_3H8UUe4f7h.png',
            'business_icon/1746103787_c7J7kSWbJN.png',
            'business_icon/1746103760_rpmXVNYcF2.png',
            'business_icon/1746103678_aBqoscEoLL.png',
            'business_icon/1748244192_NT3NodH2Zg.png',
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
            'images/default.png',
            'front/img/top-rate-img2.svg',
            'front/img/big-asana.png',
            'front/img/sftare-img1.svg',
            'front/img/poplr-zero.svg',
            'front/img/lyt-rd-grey.svg',
            'front/img/slider1_img.svg',
            'front/img/slider2_img.svg',
            'front/img/slider3_img.svg',
            'business_icon/1746103824_3H8UUe4f7h.png',
            'business_icon/1746103787_c7J7kSWbJN.png',
            'business_icon/1746103760_rpmXVNYcF2.png',
            'business_icon/1746103678_aBqoscEoLL.png',
            'business_icon/1748244192_NT3NodH2Zg.png',
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
        return $this->approved_reviews_count >= 10;
    }


    /**
     * Resolve Option 3 and Option 4 labels for the "At a glance" box.
     * Checks subcategory first (overwriting parent), then falls back to parent category.
     */
    public function getGlanceLabels(?int $langId = null): array
    {
        $langId = $langId ?? (function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : 1);
        $option3 = null;
        $option4 = null;

        // 1. Check primary category or subcategory
        $primaryCat = $this->relationLoaded('category') ? $this->category : ($this->category_id ? \App\Models\Category::find($this->category_id) : null);

        // 2. Check attached subcategories
        $subCategories = $this->relationLoaded('subCategories') ? $this->subCategories : $this->subCategories()->get();

        // Priority 1: If primary category is a subcategory, check it first
        if ($primaryCat && $primaryCat->parent_id) {
            $labels = $primaryCat->getGlanceLabels($langId);
            $option3 = $labels['option_3'];
            $option4 = $labels['option_4'];
        }

        // Priority 2: Check attached subcategories that belong to primary category (child subcategories)
        if (($option3 === null || $option4 === null) && $primaryCat && $subCategories->isNotEmpty()) {
            foreach ($subCategories as $subCat) {
                if ((int)$subCat->parent_id === (int)$primaryCat->id) {
                    $subLabels = $subCat->getGlanceLabels($langId);
                    if ($option3 === null && !empty($subLabels['option_3'])) {
                        $option3 = $subLabels['option_3'];
                    }
                    if ($option4 === null && !empty($subLabels['option_4'])) {
                        $option4 = $subLabels['option_4'];
                    }
                    if ($option3 !== null && $option4 !== null) break;
                }
            }
        }

        // Priority 3: Primary category itself (e.g. Web hosting or Website builders)
        if (($option3 === null || $option4 === null) && $primaryCat) {
            $parentLabels = $primaryCat->getGlanceLabels($langId);
            if ($option3 === null && !empty($parentLabels['option_3'])) {
                $option3 = $parentLabels['option_3'];
            }
            if ($option4 === null && !empty($parentLabels['option_4'])) {
                $option4 = $parentLabels['option_4'];
            }
        }

        // Priority 4: Any other attached subcategories (last resort fallback)
        if (($option3 === null || $option4 === null) && $subCategories->isNotEmpty()) {
            foreach ($subCategories as $subCat) {
                $subLabels = $subCat->getGlanceLabels($langId);
                if ($option3 === null && !empty($subLabels['option_3'])) {
                    $option3 = $subLabels['option_3'];
                }
                if ($option4 === null && !empty($subLabels['option_4'])) {
                    $option4 = $subLabels['option_4'];
                }
                if ($option3 !== null && $option4 !== null) break;
            }
        }

        return [
            'option_3' => $option3 ?: null,
            'option_4' => $option4 ?: null,
        ];
    }

    /**
     * Business Claiming & Team Management Relationships
     */
    public function claims()
    {
        return $this->hasMany(BusinessClaim::class);
    }

    public function members()
    {
        return $this->hasMany(BusinessMember::class);
    }

    public function activeMembers()
    {
        return $this->hasMany(BusinessMember::class)->where('status', BusinessMember::STATUS_ACTIVE);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_members')
            ->withPivot(['role', 'job_title', 'work_email', 'permissions', 'status'])
            ->withTimestamps();
    }

    public function reviewReplies()
    {
        return $this->hasMany(BusinessReviewReply::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(BusinessCorrectionRequest::class);
    }

    public function reviewInvitations()
    {
        return $this->hasMany(BusinessReviewInvitation::class);
    }

    /**
     * Resolve the normalized domain of this business for email domain validation.
     * E.g. "https://www.bluehost.com/special" -> "bluehost.com"
     */
    public function getWebsiteDomainAttribute(): string
    {
        $candidate = trim((string)($this->website ?: ($this->permanent_url ?: ($this->affiliate_link ?: ''))));

        if (empty($candidate)) {
            // Check screenshot_urls if available
            if (!empty($this->screenshot_urls) && is_array($this->screenshot_urls)) {
                $candidate = trim((string)$this->screenshot_urls[0]);
            }
        }

        if (empty($candidate)) {
            return '';
        }

        if (!preg_match('#^https?://#i', $candidate)) {
            $candidate = 'https://' . $candidate;
        }

        $host = parse_url($candidate, PHP_URL_HOST);
        if (!$host) {
            return '';
        }

        // Lowercase and strip leading 'www.'
        $host = strtolower(trim($host));
        return preg_replace('/^www\./', '', $host);
    }

    /**
     * Check if a given email's domain matches this business.
     * e.g. "john@bluehost.com" matches domain "bluehost.com"
     */
    public function matchesEmailDomain(?string $email): bool
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $parts = explode('@', strtolower(trim($email)));
        $emailDomain = end($parts);
        $emailDomain = preg_replace('/^www\./', '', $emailDomain);

        $bizDomain = $this->website_domain;
        if (empty($bizDomain)) {
            return false;
        }

        return $emailDomain === $bizDomain || str_ends_with($emailDomain, '.' . $bizDomain);
    }
}
