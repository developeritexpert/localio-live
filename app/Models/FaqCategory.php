<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;
    protected $table = 'faqs_categories';
    protected $fillable = ['status'];

    public function translations()
    {
        return $this->hasMany(FaqCategoryTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(FaqCategoryTranslation::class)
            ->where('lang_id', session('lang_id', 1));
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'faqs_category_id');
    }
}
