<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'price', 'tenure'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
