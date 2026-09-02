<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_prompt',
        'type',
        'updated_prompt',
        'description',
        'is_active',
        'openai_prompt_id',
        'anthropic_prompt_id',
        'content_field',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Content generation field types
    const CONTENT_FIELDS = ['content', 'faqs', 'features', 'pros_cons', 'metatags'];

    // Label map for content fields
    const FIELD_LABELS = [
        'content'   => 'Content / Overview',
        'faqs'      => 'FAQs',
        'features'  => 'Features',
        'pros_cons' => 'Pros & Cons',
        'metatags'  => 'Meta Tags (SEO)',
    ];

    public function getProcessedPrompt($variables = [])
    {
        $prompt = $this->original_prompt;
        foreach ($variables as $key => $value) {
            $prompt = str_replace('{' . $key . '}', $value, $prompt);
        }
        return $prompt;
    }

    public function promptAttach()
    {
        return $this->hasMany(AiPromptAttach::class);
    }

    /**
     * Get the prompt configured for a specific content field.
     */
    public static function forContentField(string $field): ?self
    {
        return static::where('content_field', $field)->where('is_active', true)->first();
    }

    /**
     * Check if this prompt has a saved provider prompt ID (no text needed in request).
     */
    public function hasOpenAIPromptId(): bool
    {
        return !empty($this->openai_prompt_id);
    }

    public function hasAnthropicPromptId(): bool
    {
        return !empty($this->anthropic_prompt_id);
    }
}
