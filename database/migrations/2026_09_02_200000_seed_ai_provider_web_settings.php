<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only insert if not already present (idempotent)
        $existing = DB::table('web_settings')
            ->whereIn('type', ['ai_openai', 'ai_anthropic'])
            ->count();

        if ($existing > 0) {
            return;
        }

        DB::table('web_settings')->insert([
            // ---- OpenAI ----
            [
                'name'       => 'API Key',
                'key'        => 'openai_api_key',
                'value'      => '',
                'type'       => 'ai_openai',
                'field_type' => 'password',
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Default Model',
                'key'        => 'openai_default_model',
                'value'      => 'gpt-4o',
                'type'       => 'ai_openai',
                'field_type' => 'text',
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ---- Anthropic ----
            [
                'name'       => 'API Key',
                'key'        => 'anthropic_api_key',
                'value'      => '',
                'type'       => 'ai_anthropic',
                'field_type' => 'password',
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Default Model',
                'key'        => 'anthropic_default_model',
                'value'      => 'claude-opus-4-5',
                'type'       => 'ai_anthropic',
                'field_type' => 'text',
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('web_settings')
            ->whereIn('type', ['ai_openai', 'ai_anthropic'])
            ->delete();
    }
};
