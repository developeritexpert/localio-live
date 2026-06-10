<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategoryTranslation extends Model
{
    use HasFactory;
    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
    // public function category(){
    //     return $this->hasOne(Country::class,'id','category_id');
    // }
    protected $fillable = ['article_category_id', 'language_id', 'name', 'description', 'slug'];

}
