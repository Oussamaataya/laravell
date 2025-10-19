<?php

return [
    // driver: 'local' (fallback heuristics) or 'openai'
    'driver' => env('AI_DRIVER', 'local'),

    // OpenAI settings
    'openai' => [
        'key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'moderation_model' => env('OPENAI_MODERATION_MODEL', 'omni-moderation-latest'),
        'base_uri' => env('OPENAI_API_BASE', 'https://api.openai.com/v1'),
    ],
];
