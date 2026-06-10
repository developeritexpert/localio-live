<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCategoryTopicTranslation extends Model
{
    
    use HasFactory;
    
    protected $fillable = ['business_category_topic_id', 'lang_id', 'title'];

    public function topic()
{
    return $this->belongsTo(BusinessCategoryTopic::class, 'business_category_topic_id');
}

}
