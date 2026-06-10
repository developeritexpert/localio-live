<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeatureTransalte;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;
use App\Models\ProductPrice;
use App\Models\VideoMedia;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getFinalPriceAttribute(): float
    {
        return \App\Services\ProductPriceService::getEffectivePrice($this);
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_product');
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id');
    }
    public function translations()
    {
        return $this->hasOne(ProductTranslation::class);
    }
    public function filterOptions()
    {
        return $this->hasMany(ProductFilterOption::class, 'product_id');
    }
    public function translation()
    {
        return $this->hasOne(ProductTranslation::class);
    }
    public function base_price()
    {
        return $this->hasOne(ProductPrice::class)
            ->where('price_type', 'base_price');
    }
    public function translationsData()
    {
        return $this->hasMany(ProductTranslation::class, 'product_id');
    }
    public function product_features()
    {
        return $this->hasMany(ProductFeature::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'product_features', 'product_id', 'feature_id');
    }
    public function iconMedia()
    {
        return $this->belongsTo(Media::class, 'product_icon', 'id');
    }

    public function imageMedia()
    {
        return $this->belongsTo(Media::class, 'product_image');
    }

    public function getProductIconAttribute($mediaId)
    {
        $media = Media::find($mediaId);

        if ($media) {
            return asset($media->dir_path . '/' . $media->file_name);
        }

        return null;
    }

    public function getProductImageAttribute($mediaId)
    {
        $media = Media::find($mediaId);

        if ($media) {
            return asset($media->dir_path . '/' . $media->file_name);
        }

        return null;
    }
    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id', 'id');
    }
    public function exclusiveDeals()
    {
        return $this->hasMany(ExclusiveDeal::class, 'applies_to_id')
            ->where('applies_to_type', 'product');
    }

    public function filters()
    {
        return $this->hasMany(ProductFilterOption::class, 'product_id', 'id');
    }

    public function getNameAttribute()
    {
        return $this->translation()->where('lang_id', $this->lang_id)->first()?->name ?? 'Unnamed Product';
    }
    public function getSlugAttribute()
    {
        return $this->translations()->where('lang_id', $this->lang_id)->first()?->slug ?? null;
    }
    public function getDescriptionAttribute()
    {
        return $this->translations()->where('lang_id', $this->lang_id)->first()?->description ?? null;
    }
    public function getOverviewAttribute()
    {
        return $this->translations()->where('lang_id', $this->lang_id)->first()?->overview ?? null;
    }
    public function getLocationAttribute()
    {
        return $this->translations()->where('lang_id', $this->lang_id)->first()?->overview ?? null;
    }
    public function getAddressAttribute()
    {
        return $this->translations()->where('lang_id', $this->lang_id)->first()?->overview ?? null;
    }
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'product_countries');
    }
    public function isAvailableInCountry($countryCode)
    {
        // If no countries are associated, product is available everywhere
        if ($this->countries()->count() === 0) {
            return true;
        }

        // Check if product is available in the specified country
        return $this->countries()
            ->where('code', $countryCode)
            ->where('pivot_status', 'active')
            ->exists();
    }
    public function scopeAvailableInCountry(Builder $query, string $countryCode)
    {
        return $query->where(function ($query) use ($countryCode) {
            // Products with no country restrictions (available worldwide)
            $query->whereDoesntHave('countries')
                // OR products specifically available in this country
                ->orWhereHas('countries', function ($query) use ($countryCode) {
                    $query->where('code', $countryCode)
                        ->where('product_countries.status', 'active');
                });
        });
    }

 

}
