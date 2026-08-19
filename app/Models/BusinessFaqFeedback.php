<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFaqFeedback extends Model
{
    use HasFactory;

    protected $table = 'business_faq_feedbacks';

    protected $fillable = [
        'business_faq_id',
        'user_id',
        'is_helpful',
        'report_reason',
        'report_details',
        'ip_address'
    ];

    protected $casts = [
        'is_helpful' => 'boolean'
    ];

    public function businessFaq()
    {
        return $this->belongsTo(BusinessFaq::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
