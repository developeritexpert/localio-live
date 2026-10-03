<?php

return [
    /*
    |----------------------------------------------------------------------
    | OpenAI API Key
    |----------------------------------------------------------------------
    */
    'api_key' => env('OPENAI_API_KEY'),

    /*
    |----------------------------------------------------------------------
    | Moderation Model (Layer 2 — Free)
    |----------------------------------------------------------------------
    */
    'moderation_model' => env('OPENAI_MODERATION_MODEL', 'omni-moderation-latest'),

    /*
    |----------------------------------------------------------------------
    | Review Integrity AI Model (Layer 3 — Batch)
    |----------------------------------------------------------------------
    */
    'review_ai_model'          => env('OPENAI_REVIEW_AI_MODEL', 'gpt-5.6-luna'),
    'review_prompt_id'         => env('OPENAI_REVIEW_PROMPT_ID', ''),
    'review_prompt_version'    => env('OPENAI_REVIEW_PROMPT_VERSION', '1'),
    'review_reasoning_effort'  => env('OPENAI_REVIEW_REASONING_EFFORT', 'low'),
    'review_batch_hours'       => (int) env('REVIEW_MODERATION_BATCH_HOURS', 12),

    /*
    |----------------------------------------------------------------------
    | Supported Localio Locales (sent to GPT for locale detection)
    |----------------------------------------------------------------------
    */
    'supported_locales' => [
        'en-US', 'en-GB', 'en-AU', 'en-CA',
        'de-DE',
        'es-ES', 'es-419',
        'pt-BR', 'pt-PT',
        'fr-FR',
        'it-IT',
        'nl-NL',
        'pl-PL',
        'ru-RU',
        'ja-JP',
        'zh-CN', 'zh-TW',
        'ar-SA',
        'ko-KR',
        'tr-TR',
        'sv-SE',
        'da-DK',
        'fi-FI',
        'nb-NO',
    ],
];
