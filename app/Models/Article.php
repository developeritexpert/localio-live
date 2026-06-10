<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'image', 'status','category_id'];
    public function translations()
    {
        return $this->hasMany(ArticleTranslation::class);
    }
    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id'); // 'category_id' is the foreign key
    }
}
