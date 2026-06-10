<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    protected $fillable = [
        'question','answer','status','faqs_category_id','type','position'
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc');
    }


    public function translations()
    {
        return $this->hasMany(FaqTranslation::class,'faq_id');
    }

    public function translation()
    {
        return $this->hasOne(FaqTranslation::class)
            ->where('lang_id', session('lang_id', 1));
    }
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faqs_category_id');
    }

}
