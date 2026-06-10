<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFaqTranslation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'business_faq_id',
        'lang_id',
        'question',
        'answer'
    ];

    public function businessFaq()
    {
        return $this->belongsTo(BusinessFaq::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

}
