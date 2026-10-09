<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Statuses
    const STATUS_PROCESSING     = 'processing';
    const STATUS_PUBLISHED      = 'published';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_REJECTED       = 'rejected';
    const STATUS_REMOVED        = 'removed';

    // Escalation Sources
    const ESCALATION_LARAVEL        = 'laravel_rule';
    const ESCALATION_MODERATION_API = 'openai_moderation';
    const ESCALATION_LOCALIO_AI     = 'localio_ai';
    const ESCALATION_USER_REPORT    = 'user_report';
    const ESCALATION_BIZ_REPORT     = 'business_report';
    const ESCALATION_MANUAL         = 'manual';

    // AI Statuses
    const AI_STATUS_WAITING    = 'waiting';
    const AI_STATUS_PROCESSING = 'processing';
    const AI_STATUS_DONE       = 'done';
    const AI_STATUS_SKIPPED    = 'skipped';

    // Locale Statuses
    const LOCALE_MATCH        = 'MATCH';
    const LOCALE_REASSIGNED   = 'REASSIGNED';
    const LOCALE_HUMAN_REVIEW = 'HUMAN_REVIEW';

    // Risk Levels
    const RISK_LOW      = 'LOW';
    const RISK_MEDIUM   = 'MEDIUM';
    const RISK_HIGH     = 'HIGH';
    const RISK_CRITICAL = 'CRITICAL';

    // Appeal Statuses
    const APPEAL_OPEN         = 'open';
    const APPEAL_UNDER_REVIEW = 'under_review';
    const APPEAL_RESOLVED     = 'resolved';

    protected $fillable = [
        'business_id',
        'lang_id',
        'user_id',

        'ease_of_use_rating',
        'value_for_money_rating',
        'customer_service_rating',
        'exclusive_service_rating',
        'rating',
        'recommend',
        'status',
        'public_name',

        // Layer 1
        'laravel_check_status',
        'laravel_fail_reason',

        // Layer 2
        'moderation_api_passed',
        'moderation_api_result',
        'moderation_api_checked_at',

        // Layer 3
        'localio_ai_status',
        'localio_ai_batch_id',
        'localio_ai_decision',
        'localio_ai_risk_level',
        'localio_ai_flags',
        'localio_ai_confidence',
        'localio_ai_reason',
        'submitted_locale',
        'source_locale',
        'locale_status',
        'localio_ai_model',
        'localio_ai_prompt_id',
        'localio_ai_prompt_version',
        'localio_ai_processed_at',

        // Overall
        'escalation_source',
        'appeal_status',
    ];

    protected $casts = [
        'moderation_api_result'     => 'array',
        'localio_ai_flags'          => 'array',
        'moderation_api_passed'     => 'boolean',
        'moderation_api_checked_at' => 'datetime',
        'localio_ai_processed_at'   => 'datetime',
        'localio_ai_confidence'     => 'float',
        'recommend'                 => 'boolean',
    ];

    // Scopes
    public function scopeReadyForBatch($query)
    {
        return $query->where('status', self::STATUS_PROCESSING)
                     ->where('moderation_api_passed', true)
                     ->where('localio_ai_status', self::AI_STATUS_WAITING);
    }

    public function scopePendingModeration($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', [self::STATUS_PUBLISHED, 'active']);
    }

    public function scopePubliclyVisible($query)
    {
        return $query->whereIn('status', [self::STATUS_PUBLISHED, 'active']);
    }

    // Relationships
    public function ratings()
    {
        return $this->hasMany(ReviewRating::class, 'review_id');
    }

    public function selectedProCons()
    {
        return $this->belongsToMany(CategoryProCon::class, 'review_pro_cons', 'review_id', 'category_pro_con_id')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function translations()
    {
        return $this->hasMany(ReviewTranslation::class, 'reviews_id');
    }

    public function translation($lang_id)
    {
        return $this->hasOne(ReviewTranslation::class, 'reviews_id', 'id')->where('language_id', $lang_id);
    }

    public function original()
    {
        return $this->hasOne(ReviewTranslation::class, 'reviews_id', 'id')
            ->join('reviews', 'review_translations.reviews_id', '=', 'reviews.id')
            ->whereColumn('review_translations.language_id', 'reviews.lang_id')
            ->select('review_translations.*');
    }

    public function moderationLogs()
    {
        return $this->hasMany(ReviewModerationLog::class, 'review_id')->orderBy('created_at', 'desc');
    }

    public function reports()
    {
        return $this->hasMany(ReviewReport::class, 'review_id')->orderBy('created_at', 'desc');
    }

    public function appeals()
    {
        return $this->hasMany(ReviewAppeal::class, 'review_id')->orderBy('created_at', 'desc');
    }

    public function moderationCases()
    {
        return $this->belongsToMany(ModerationCase::class, 'moderation_case_reviews', 'review_id', 'case_id')->withTimestamps();
    }
}
