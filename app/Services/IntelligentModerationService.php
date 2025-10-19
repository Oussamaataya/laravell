<?php

namespace App\Services;

class IntelligentModerationService
{
    private array $badWordsPatterns;
    private array $contextualRules;
    private array $l33tMap;
    private int $severityThreshold;

    public function __construct()
    {
        $this->initializeBadWordsPatterns();
        $this->initializeContextualRules();
        $this->initializeL33tMap();
        $this->severityThreshold = config('moderation.severity_threshold', 70);
    }

    /**
     * Analyse intelligente du contenu avec scoring et détection contextuelle
     */
    public function analyzeContent(string $text): array
    {
        $normalizedText = $this->normalizeText($text);
        $detectedWords = $this->detectBadWords($normalizedText);
        $contextScore = $this->analyzeContext($normalizedText);
        $severityScore = $this->calculateSeverityScore($detectedWords, $contextScore);
        
        return [
            'is_inappropriate' => $severityScore >= $this->severityThreshold,
            'severity_score' => $severityScore,
            'detected_words' => $detectedWords,
            'context_analysis' => $contextScore,
            'suggestions' => $this->generateSuggestions($detectedWords),
            'confidence' => $this->calculateConfidence($detectedWords, $contextScore)
        ];
    }

    /**
     * Normalise le texte pour détecter les contournements
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remplacer les caractères l33t speak
        foreach ($this->l33tMap as $leet => $normal) {
            $text = str_replace($leet, $normal, $text);
        }
        
        // Supprimer les espaces, tirets et caractères spéciaux entre les lettres
        $text = preg_replace('/([a-z])[^a-z0-9]+([a-z])/u', '$1$2', $text);
        
        // Supprimer les répétitions de caractères (ex: coooon -> con)
        $text = preg_replace('/(.)\1{2,}/u', '$1$1', $text);
        
        return $text;
    }

    /**
     * Détecte les mots inappropriés avec leurs variantes
     */
    private function detectBadWords(string $text): array
    {
        $detectedWords = [];
        
        foreach ($this->badWordsPatterns as $category => $patterns) {
            foreach ($patterns as $pattern => $severity) {
                if (preg_match_all($pattern, $text, $matches)) {
                    foreach ($matches[0] as $match) {
                        $detectedWords[] = [
                            'word' => $match,
                            'category' => $category,
                            'severity' => $severity,
                            'pattern' => $pattern
                        ];
                    }
                }
            }
        }
        
        return $detectedWords;
    }

    /**
     * Analyse le contexte pour détecter l'intention
     */
    private function analyzeContext(string $text): array
    {
        $contextScore = [
            'aggressive_tone' => 0,
            'hate_speech' => 0,
            'sexual_content' => 0,
            'harassment' => 0,
            'spam_indicators' => 0
        ];

        foreach ($this->contextualRules as $category => $rules) {
            foreach ($rules as $rule) {
                if (preg_match($rule['pattern'], $text)) {
                    $contextScore[$category] += $rule['weight'];
                }
            }
        }

        return $contextScore;
    }

    /**
     * Calcule le score de sévérité global
     */
    private function calculateSeverityScore(array $detectedWords, array $contextScore): int
    {
        $score = 0;
        
        // Score basé sur les mots détectés
        foreach ($detectedWords as $word) {
            $score += $word['severity'];
        }
        
        // Bonus/malus contextuel
        $contextBonus = array_sum($contextScore) * 0.3;
        $score += $contextBonus;
        
        // Multiplicateur si plusieurs mots de même catégorie
        $categories = array_column($detectedWords, 'category');
        $categoryCount = array_count_values($categories);
        foreach ($categoryCount as $count) {
            if ($count > 1) {
                $score *= 1.2; // 20% de bonus par catégorie répétée
            }
        }
        
        return min(100, max(0, (int)$score));
    }

    /**
     * Calcule la confiance dans la détection
     */
    private function calculateConfidence(array $detectedWords, array $contextScore): int
    {
        $confidence = 50; // Base confidence
        
        // Plus de mots détectés = plus de confiance
        $confidence += count($detectedWords) * 10;
        
        // Contexte fort = plus de confiance
        $maxContext = max($contextScore);
        $confidence += $maxContext * 2;
        
        return min(100, max(0, $confidence));
    }

    /**
     * Génère des suggestions pour améliorer le contenu
     */
    private function generateSuggestions(array $detectedWords): array
    {
        $suggestions = [];
        
        $replacements = [
            'merde' => ['zut', 'flûte', 'diantre'],
            'con' => ['bête', 'idiot'],
            'connard' => ['imbécile', 'crétin'],
            'putain' => ['bon sang', 'saperlipopette'],
            'salaud' => ['malotru', 'goujat']
        ];
        
        foreach ($detectedWords as $wordData) {
            $word = $wordData['word'];
            if (isset($replacements[$word])) {
                $suggestions[] = [
                    'original' => $word,
                    'alternatives' => $replacements[$word],
                    'message' => "Essayez de remplacer '{$word}' par une alternative plus appropriée."
                ];
            }
        }
        
        return $suggestions;
    }

    /**
     * Initialise les patterns de mots inappropriés par catégorie
     */
    private function initializeBadWordsPatterns(): void
    {
        $this->badWordsPatterns = [
            'profanity' => [
                '/\b(m+e+r+d+e+|m3rd3|m€rd€)\b/iu' => 60,
                '/\b(p+u+t+a+i+n+|put41n|put@in)\b/iu' => 70,
                '/\b(c+o+n+n+a+r+d+|c0nn4rd|conn@rd)\b/iu' => 80,
                '/\b(s+a+l+a+u+d+|s4l4ud|sal@ud)\b/iu' => 75,
                '/\b(e+n+c+u+l+é+|3ncul3|encul€)\b/iu' => 90,
                '/\b(s+a+l+o+p+e+|s4l0p3|sal0p€)\b/iu' => 85,
            ],
            'insults' => [
                '/\b(c+o+n+|c0n)\b/iu' => 50,
                '/\b(i+d+i+o+t+|1d10t)\b/iu' => 40,
                '/\b(c+r+é+t+i+n+|cr3t1n)\b/iu' => 45,
                '/\b(d+é+b+i+l+e+|d3b1l3)\b/iu' => 40,
            ],
            'hate_speech' => [
                '/\b(ta\s*m+è+r+e+|t4\s*m3r3)\b/iu' => 85,
                '/\b(f+d+p+|fdp)\b/iu' => 80,
                '/\b(n+i+q+u+e+|n1qu3)\b/iu' => 75,
            ],
            'sexual' => [
                '/\b(b+i+t+e+|b1t3)\b/iu' => 70,
                '/\b(s+u+c+e+|suc3)\b/iu' => 65,
            ]
        ];
    }

    /**
     * Initialise les règles contextuelles
     */
    private function initializeContextualRules(): void
    {
        $this->contextualRules = [
            'aggressive_tone' => [
                ['pattern' => '/\b(va\s*(te\s*)?faire|crève|ferme\s*ta\s*gueule)\b/iu', 'weight' => 15],
                ['pattern' => '/[!]{3,}/', 'weight' => 5],
                ['pattern' => '/[A-Z]{5,}/', 'weight' => 8], // CAPS LOCK
            ],
            'hate_speech' => [
                ['pattern' => '/\b(je\s*te\s*hais|tu\s*me\s*dégoûtes)\b/iu', 'weight' => 20],
                ['pattern' => '/\b(tous\s*les\s*\w+\s*sont)\b/iu', 'weight' => 15],
            ],
            'harassment' => [
                ['pattern' => '/\b(arrête\s*de|laisse\s*moi|fiche\s*moi\s*la\s*paix)\b/iu', 'weight' => 10],
                ['pattern' => '/\b(tu\s*vas\s*voir|je\s*vais\s*te)\b/iu', 'weight' => 18],
            ],
            'spam_indicators' => [
                ['pattern' => '/(.)\1{10,}/', 'weight' => 12], // Répétitions excessives
                ['pattern' => '/\b(clique\s*ici|gratuit|promo|urgent)\b/iu', 'weight' => 8],
            ]
        ];
    }

    /**
     * Initialise la carte de conversion l33t speak
     */
    private function initializeL33tMap(): void
    {
        $this->l33tMap = [
            '3' => 'e',
            '4' => 'a',
            '1' => 'i',
            '0' => 'o',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '€' => 'e',
            '$' => 's',
            '!' => 'i',
            '8' => 'b',
            '6' => 'g',
            '9' => 'g',
            '+' => 't',
            '|' => 'l',
            '/' => 'l',
            '\\' => 'l',
            '(' => 'c',
            ')' => 'd',
            '<' => 'c',
            '>' => 'd',
            '{' => 'c',
            '}' => 'd',
            '[' => 'c',
            ']' => 'd'
        ];
    }

    /**
     * Obtient les statistiques de modération
     */
    public function getModerationStats(): array
    {
        return [
            'total_patterns' => array_sum(array_map('count', $this->badWordsPatterns)),
            'categories' => array_keys($this->badWordsPatterns),
            'severity_threshold' => $this->severityThreshold,
            'contextual_rules' => array_sum(array_map('count', $this->contextualRules))
        ];
    }

    /**
     * Met à jour le seuil de sévérité
     */
    public function setSeverityThreshold(int $threshold): void
    {
        $this->severityThreshold = max(0, min(100, $threshold));
    }
}
