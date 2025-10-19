<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Moderation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour le système de modération intelligent
    |
    */

    // Seuil de sévérité (0-100) au-dessus duquel le contenu est rejeté
    'severity_threshold' => env('MODERATION_SEVERITY_THRESHOLD', 70),

    // Seuil de confiance minimum pour les détections automatiques
    'confidence_threshold' => env('MODERATION_CONFIDENCE_THRESHOLD', 80),

    // Mode strict (rejette aussi les contenus avec score modéré)
    'strict_mode' => env('MODERATION_STRICT_MODE', false),

    // Activer les suggestions de remplacement
    'enable_suggestions' => env('MODERATION_ENABLE_SUGGESTIONS', true),

    // Activer la détection contextuelle
    'enable_context_analysis' => env('MODERATION_ENABLE_CONTEXT', true),

    // Activer la détection de contournements (l33t speak, etc.)
    'enable_bypass_detection' => env('MODERATION_ENABLE_BYPASS_DETECTION', true),

    // Logging des détections pour amélioration du système
    'log_detections' => env('MODERATION_LOG_DETECTIONS', true),

    // Actions selon le score de sévérité
    'actions' => [
        'low' => [      // 0-40
            'action' => 'allow',
            'message' => null
        ],
        'medium' => [   // 41-69
            'action' => 'warn',
            'message' => 'Attention : votre contenu pourrait être considéré comme inapproprié.'
        ],
        'high' => [     // 70-89
            'action' => 'block',
            'message' => 'Votre contenu contient du langage inapproprié et ne peut pas être publié.'
        ],
        'critical' => [ // 90-100
            'action' => 'block_and_report',
            'message' => 'Votre contenu viole nos conditions d\'utilisation.'
        ]
    ],

    // Catégories de contenu inapproprié et leurs poids
    'categories' => [
        'profanity' => [
            'weight' => 1.0,
            'enabled' => true,
            'description' => 'Langage vulgaire et grossier'
        ],
        'insults' => [
            'weight' => 0.8,
            'enabled' => true,
            'description' => 'Insultes et attaques personnelles'
        ],
        'hate_speech' => [
            'weight' => 1.5,
            'enabled' => true,
            'description' => 'Discours de haine et discrimination'
        ],
        'sexual' => [
            'weight' => 1.2,
            'enabled' => true,
            'description' => 'Contenu sexuel explicite'
        ],
        'harassment' => [
            'weight' => 1.3,
            'enabled' => true,
            'description' => 'Harcèlement et menaces'
        ]
    ],

    // Whitelist - mots qui peuvent sembler inappropriés mais sont acceptables dans certains contextes
    'whitelist' => [
        'expressions' => [
            'con comme un balai', // Expression idiomatique
            'con de nature',      // Expression courante
        ],
        'technical_terms' => [
            'console',
            'connection',
            'configuration'
        ]
    ],

    // Paramètres pour les utilisateurs de confiance
    'trusted_users' => [
        'enabled' => true,
        'threshold_reduction' => 20, // Réduction du seuil pour les utilisateurs de confiance
        'roles' => ['admin', 'moderator', 'verified']
    ]
];
