<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_id','lang_id','question', 'answer' //added  this answer
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }

    // Define the relationship with the language
    public function language()
    {
        return $this->belongsTo(Language::class,'language_id');
    }
}
