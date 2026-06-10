<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExclusiveDeal extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'applies_to_type', 'applies_to_id', 'country_code',
        'price_type', 'discount_percent', 'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'discount_percent' => 'float',
    ];
    protected $dates = ['starts_at', 'ends_at'];
    public function appliesTo(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function target()
    {
        return $this->morphTo(__FUNCTION__, 'applies_to_type', 'applies_to_id');
    }
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('starts_at', '<=', $now)
                     ->where('ends_at', '>=', $now);
    }

    // Scope for specific product
    public function scopeForProduct(Builder $query, $product): Builder
    {
        $productId = is_object($product) ? $product->id : $product;
        return $query->where('product_id', $productId);
    }

    // Scope for country
    public function scopeForCountry(Builder $query, string $countryCode): Builder
    {
        return $query->where('country_code', $countryCode);
    }

    // Scope for price type
    public function scopeForPriceType(Builder $query, string $priceType): Builder
    {
        return $query->where('price_type', $priceType);
    }
}
