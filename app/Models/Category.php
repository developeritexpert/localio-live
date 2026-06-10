<?php

namespace App\Models;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [ 'image','category_icon', 'status','total_products','total_reviews'];

    protected $lang_code, $lang_id;

    public function __construct()
    {
        $this->lang_id = session()->get('lang_id');
        $this->lang_code = session()->get('lang_code');
    }
    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function media()
{
    return $this->hasOne(Media::class, 'id', 'category_icon');
}
public function iconMedia()
{
    return $this->hasOne(Media::class, 'id', 'category_icon');
}

public function imageMedia()
{
    return $this->hasOne(Media::class, 'id', 'image');
}


    public function categoryTranslations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function translation()
    {
        return $this->hasOne(CategoryTranslation::class, 'category_id');
    }
    public function businesses()
    {
        return $this->hasMany(Business::class, 'category_id');
    }
    public function businessesWithReviews()
    {
        return $this->hasMany(Business::class)
                    ->whereHas('reviews');
    }
    public function translations()
    {
        $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id');

        if(!$category){
            $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id');
        }
        return $category;
    }

    public function Gettranslations($lang_id)
    {
        $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id')
        ->where('lang_id',$this->lang_id );
        if(!$category){
            $category=  $this->hasOne(CategoryTranslation::class, 'category_id', 'id')
         ->where('lang_id', 1);

        }
        return $category;
    }
    public function exclusiveDeals()
    {
        return $this->hasMany(ExclusiveDeal::class, 'applies_to_id')
            ->where('applies_to_type', 'category');
    }

    public function getNameAttribute($value)
    {
        $translation = $this->translation()->first();
        return $translation ? $translation->name : $value;
    }

    public function filters()
    {
        return $this->hasMany(Filter::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products', 'category_id', 'product_id');
    }

    public function topics()
    {
        return $this->hasMany(BusinessCategoryTopic::class, 'category_id');
    }
    


}
