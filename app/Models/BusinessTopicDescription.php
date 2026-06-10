<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTopicDescription extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'topic_id', 'description'];

    public function topic()
    {
        return $this->belongsTo(BusinessCategoryTopic::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
