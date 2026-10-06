<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\StaticContentKey;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keys = [
            [
                'key' => 'join_conversation_title',
                'name' => 'Join the Conversation Box Title',
                'default_value' => 'Join the conversation',
                'field_type' => 'text',
            ],
            [
                'key' => 'join_conversation_desc',
                'name' => 'Join the Conversation Box Description',
                'default_value' => 'Connect with other users to ask questions, share experiences, and exchange useful tips.',
                'field_type' => 'textarea',
            ],
            [
                'key' => 'start_discussion_button',
                'name' => 'Start a Discussion Button Text',
                'default_value' => 'Start a discussion',
                'field_type' => 'text',
            ],
        ];

        foreach ($keys as $k) {
            StaticContentKey::updateOrCreate(
                ['key' => $k['key']],
                [
                    'name' => $k['name'],
                    'default_value' => $k['default_value'],
                    'field_type' => $k['field_type'],
                ]
            );
        }

        Cache::forget('static_texts_all');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        StaticContentKey::whereIn('key', [
            'join_conversation_title',
            'join_conversation_desc',
            'start_discussion_button',
        ])->delete();

        Cache::forget('static_texts_all');
    }
};
