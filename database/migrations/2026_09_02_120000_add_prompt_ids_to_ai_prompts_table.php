<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            // Provider-side saved prompt IDs (referenced by ID, prompt text lives in ChatGPT/Claude)
            $table->string('openai_prompt_id')->nullable()->after('updated_prompt')
                  ->comment('Saved prompt ID from OpenAI Prompt Management (e.g. pmpt_xxxx)');
            $table->string('anthropic_prompt_id')->nullable()->after('openai_prompt_id')
                  ->comment('Saved prompt ID from Anthropic (if supported)');
            // Maps to a specific content field in content generation
            $table->string('content_field')->nullable()->after('anthropic_prompt_id')
                  ->comment('Which content field this prompt generates: content|faqs|features|pros_cons|metatags');
        });
    }

    public function down(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->dropColumn(['openai_prompt_id', 'anthropic_prompt_id', 'content_field']);
        });
    }
};
