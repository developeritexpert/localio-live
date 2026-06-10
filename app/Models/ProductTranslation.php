<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'name', 'slug', 'description', 'overview', 'status', 'product_link', 'lang_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    public function translations()
    {
        return $this->hasMany(ProductTranslation::class, 'product_id', 'product_id')
                    ->where('id', '!=', $this->id);
    }

}
