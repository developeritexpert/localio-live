<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'lang_id', 'is_important', 'name', 'title', 'description', 'slug','worth_it_content','best_for_content','integrations_content','security_compliance_content', 'comparison_slug'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id','lang_id');
    }


}
