<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    use HasFactory;
    public function translations()
    {
        return $this->hasMany(ArticleCategoryTranslation::class);
    }
    protected $fillable = ['name', 'slug', 'description', 'image', 'status'];
    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id'); // 'category_id' is the foreign key in the articles table
    }
}
