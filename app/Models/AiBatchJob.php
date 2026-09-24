<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiBatchJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'batch_id',
        'prompt_type',
        'status',
        'submitted_at',
        'completed_at',
        'input_payload',
        'raw_response',
        'error_message',
        'total_items',
        'completed_items',
        'failed_items',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function queueItems()
    {
        return $this->hasMany(AiContentQueue::class, 'batch_job_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
