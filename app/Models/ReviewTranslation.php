<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewTranslation extends Model
{
    use HasFactory;

    const STATUS_WAITING      = 'WAITING';
    const STATUS_BATCHED      = 'BATCHED';
    const STATUS_COMPLETED    = 'COMPLETED';
    const STATUS_FAILED       = 'FAILED';
    const STATUS_NEEDS_REVIEW = 'NEEDS_REVIEW';
    const STATUS_OUTDATED     = 'OUTDATED';
    const STATUS_ORIGINAL     = 'ORIGINAL';

    protected $fillable = [
        'reviews_id',
        'review_id',
        'language_id',
        'source_locale',
        'target_locale',
        'title',
        'description',
        'pros',
        'cons',
        'translated_title',
        'translated_text',
        'status',
        'batch_id',
        'openai_custom_id',
        'model',
        'prompt_id',
        'prompt_version',
        'retry_count',
        'translated_at',
    ];

    protected $casts = [
        'retry_count'   => 'integer',
        'translated_at' => 'datetime',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'reviews_id');
    }

    public function batch()
    {
        return $this->belongsTo(TranslationBatch::class, 'batch_id');
    }

    public function getEffectiveTitleAttribute(): ?string
    {
        return !empty($this->translated_title) ? $this->translated_title : $this->title;
    }

    public function getEffectiveTextAttribute(): ?string
    {
        return !empty($this->translated_text) ? $this->translated_text : $this->description;
    }
}
