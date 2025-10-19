<?php

namespace App\Services;

class CommentToneService
{
    private IntelligentModerationService $moderationService;

    public function __construct()
    {
        $this->moderationService = new IntelligentModerationService();
    }

    /**
     * Analyze the given text and return tone and bad-words info.
     *
     * @param string $text
     * @return array [
     *   'tone' => 'POSITIF'|'NEGATIF'|'NEUTRE',
     *   'has_bad_words' => bool,
     *   'bad_words' => array,
     *   'moderation_result' => array (nouveau)
     * ]
     */
    public function analyze(string $text): array
    {
        // Utiliser le nouveau service intelligent en priorité
        $moderationResult = $this->moderationService->analyzeContent($text);
        
        // Si AI driver configuré, utiliser aussi AIModerationService pour comparaison
        $aiResult = null;
        if (config('ai.driver') === 'openai' && config('ai.openai.key')) {
            try {
                $mod = new AIModerationService();
                $aiResult = $mod->moderate($text);
            } catch (\Exception $e) {
                // ignore and continue with intelligent moderation
            }
        }

        // Analyser le ton avec l'ancienne méthode améliorée
        $tone = $this->analyzeTone($text, $moderationResult);
        
        // Extraire les mots inappropriés détectés
        $badWords = array_column($moderationResult['detected_words'], 'word');
        
        // Combiner avec les résultats AI si disponibles
        if ($aiResult && isset($aiResult['local']['bad_words'])) {
            $badWords = array_unique(array_merge($badWords, $aiResult['local']['bad_words']));
        }

        return [
            'tone' => $tone,
            'has_bad_words' => $moderationResult['is_inappropriate'] || count($badWords) > 0,
            'bad_words' => array_values($badWords),
            'moderation_result' => $moderationResult,
            'ai_result' => $aiResult
        ];
    }

    /**
     * Analyse le ton du texte avec une approche améliorée
     */
    private function analyzeTone(string $text, array $moderationResult): string
    {
        $textLower = mb_strtolower($text, 'UTF-8');

        // Mots positifs étendus
        $positiveWords = [
            'bon', 'bien', 'super', 'excellent', 'merci', 'bravo', 'heureux', 'génial', 
            'content', 'satisfait', 'adorer', 'aimer', 'formidable', 'magnifique',
            'parfait', 'fantastique', 'incroyable', 'merveilleux', 'extraordinaire',
            'top', 'cool', 'sympa', 'chouette', 'agréable', 'plaisant'
        ];

        // Mots négatifs étendus
        $negativeWords = [
            'mauvais', 'nul', 'horrible', 'pire', 'déçu', 'décevant', 'problème', 
            'problèmes', 'haine', 'haïr', 'détester', 'péjoratif', 'chiant',
            'affreux', 'terrible', 'catastrophique', 'lamentable', 'pathétique',
            'minable', 'pourri', 'dégueulasse', 'répugnant', 'écœurant'
        ];

        // Tokenize words (amélioré)
        $words = preg_split('/[^\p{L}0-9]+/u', $textLower, -1, PREG_SPLIT_NO_EMPTY);

        $posCount = 0;
        $negCount = 0;

        foreach ($words as $w) {
            if (in_array($w, $positiveWords, true)) {
                $posCount++;
            }
            if (in_array($w, $negativeWords, true)) {
                $negCount++;
            }
        }

        // Facteur contextuel basé sur la modération
        $contextFactor = 0;
        if (isset($moderationResult['context_analysis'])) {
            $contextAnalysis = $moderationResult['context_analysis'];
            if ($contextAnalysis['aggressive_tone'] > 10 || $contextAnalysis['hate_speech'] > 15) {
                $contextFactor = -2; // Force vers négatif
            }
        }

        // Décision du ton avec contexte
        $netSentiment = $posCount - $negCount + $contextFactor;
        
        if ($netSentiment > 1) {
            return 'POSITIF';
        } elseif ($netSentiment < -1 || $moderationResult['severity_score'] > 50) {
            return 'NEGATIF';
        }
        
        return 'NEUTRE';
    }

    /**
     * Obtient une analyse détaillée pour l'administration
     */
    public function getDetailedAnalysis(string $text): array
    {
        $basicAnalysis = $this->analyze($text);
        $moderationStats = $this->moderationService->getModerationStats();
        
        return array_merge($basicAnalysis, [
            'detailed_analysis' => [
                'text_length' => mb_strlen($text),
                'word_count' => str_word_count($text),
                'moderation_stats' => $moderationStats,
                'severity_breakdown' => $this->getSeverityBreakdown($basicAnalysis['moderation_result']),
                'recommendations' => $this->getRecommendations($basicAnalysis['moderation_result'])
            ]
        ]);
    }

    /**
     * Décompose le score de sévérité par catégorie
     */
    private function getSeverityBreakdown(array $moderationResult): array
    {
        $breakdown = [];
        
        foreach ($moderationResult['detected_words'] as $word) {
            $category = $word['category'];
            if (!isset($breakdown[$category])) {
                $breakdown[$category] = [
                    'count' => 0,
                    'total_severity' => 0,
                    'words' => []
                ];
            }
            
            $breakdown[$category]['count']++;
            $breakdown[$category]['total_severity'] += $word['severity'];
            $breakdown[$category]['words'][] = $word['word'];
        }
        
        return $breakdown;
    }

    /**
     * Génère des recommandations pour améliorer le contenu
     */
    private function getRecommendations(array $moderationResult): array
    {
        $recommendations = [];
        
        if ($moderationResult['severity_score'] > 70) {
            $recommendations[] = 'Révisez complètement votre message pour adopter un ton plus respectueux.';
        } elseif ($moderationResult['severity_score'] > 40) {
            $recommendations[] = 'Considérez reformuler certaines parties de votre message.';
        }
        
        if (!empty($moderationResult['suggestions'])) {
            $recommendations[] = 'Utilisez les suggestions de remplacement proposées.';
        }
        
        if ($moderationResult['context_analysis']['aggressive_tone'] > 10) {
            $recommendations[] = 'Adoptez un ton moins agressif dans votre communication.';
        }
        
        return $recommendations;
    }
}
