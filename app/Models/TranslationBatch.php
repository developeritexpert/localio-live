<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationBatch extends Model
{
    use HasFactory;

    const STATUS_WAITING    = 'waiting';
    const STATUS_SUBMITTING = 'submitting';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_FAILED     = 'failed';
    const STATUS_CANCELLED  = 'cancelled';

    protected $fillable = [
        'openai_batch_id',
        'type',
        'model',
        'prompt_id',
        'prompt_version',
        'request_count',
        'status',
        'successful_count',
        'failed_count',
        'submitted_at',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'submitted_at'     => 'datetime',
        'completed_at'     => 'datetime',
        'failed_at'        => 'datetime',
        'request_count'    => 'integer',
        'successful_count' => 'integer',
        'failed_count'     => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(ReviewTranslation::class, 'batch_id');
    }
}
