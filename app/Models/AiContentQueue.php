<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiContentQueue extends Model
{
    use HasFactory;

    protected $table = 'ai_content_queue';

    protected $fillable = [
        'batch_job_id',
        'resource_type',
        'resource_id',
        'content_field',
        'status',
        'openai_request_id',
        'result',
        'error_message',
        'prompt_id',
    ];

    // Human-readable field labels
    public static array $fieldLabels = [
        'short_description' => 'Short Description',
        'long_description'  => 'Long Description',
        'usps'              => 'USPs',
        'what_is'           => 'What is',
        'pros_cons'         => 'Pros & Cons',
        'services'          => 'Services',
        'faqs'              => 'FAQs',
        'categories'        => 'Categories',
        'active_countries'  => 'Active countries/regions',
        // Legacy support
        'content'           => 'Content',
        'features'          => 'Features',
        'metatags'          => 'Metatags',
    ];

    public function batchJob()
    {
        return $this->belongsTo(AiBatchJob::class, 'batch_job_id');
    }

    public function prompt()
    {
        return $this->belongsTo(AiPrompt::class, 'prompt_id');
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('resource_type', 'business')->where('resource_id', $businessId);
    }

    public function scopeByField($query, string $field)
    {
        return $query->where('content_field', $field);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
