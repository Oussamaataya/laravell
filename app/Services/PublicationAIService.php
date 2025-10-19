<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PublicationAIService
{
    private Client $httpClient;
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->httpClient = new Client(['timeout' => 30]);
        $this->apiKey = config('ai.openai.key');
        $this->baseUrl = config('ai.openai.base_uri', 'https://api.openai.com/v1');
    }

    /**
     * Analyse complète d'une publication avec IA
     */
    public function analyzePublication(string $title, string $content, ?string $imagePath = null): array
    {
        $cacheKey = 'ai_analysis_' . md5($title . $content . $imagePath);
        
        return Cache::remember($cacheKey, 300, function () use ($title, $content, $imagePath) {
            $results = [];
            
            // Analyse du contenu textuel
            $results['content_analysis'] = $this->analyzeTextContent($title, $content);
            
            // Génération de hashtags intelligents
            $results['hashtags'] = $this->generateSmartHashtags($title, $content);
            
            // Détection de catégorie/sujet
            $results['category'] = $this->detectCategory($title, $content);
            
            // Amélioration du contenu
            $results['content_improvements'] = $this->suggestContentImprovements($title, $content);
            
            // Optimisation SEO
            $results['seo_optimization'] = $this->generateSEOOptimization($title, $content);
            
            // Analyse d'image si présente
            if ($imagePath) {
                $results['image_analysis'] = $this->analyzeImage($imagePath, $content);
            }
            
            // Score de qualité global
            $results['quality_score'] = $this->calculateQualityScore($results);
            
            return $results;
        });
    }

    /**
     * Analyse du contenu textuel avec IA
     */
    private function analyzeTextContent(string $title, string $content): array
    {
        $prompt = "Analysez cette publication et fournissez une analyse structurée en JSON :

Titre: {$title}
Contenu: {$content}

Analysez et retournez UNIQUEMENT un JSON valide avec :
{
  \"sentiment\": \"positif|neutre|negatif\",
  \"tone\": \"professionnel|casual|informatif|promotionnel|personnel\",
  \"readability_score\": 1-10,
  \"engagement_potential\": 1-10,
  \"topics\": [\"sujet1\", \"sujet2\"],
  \"target_audience\": \"description de l'audience cible\",
  \"language_quality\": 1-10,
  \"suggestions\": [\"suggestion1\", \"suggestion2\"]
}";

        try {
            $response = $this->callOpenAI($prompt, 0.3);
            $analysis = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $analysis;
            }
        } catch (\Exception $e) {
            Log::warning('AI content analysis failed: ' . $e->getMessage());
        }

        // Fallback analysis
        return $this->getFallbackContentAnalysis($title, $content);
    }

    /**
     * Génération de hashtags intelligents
     */
    private function generateSmartHashtags(string $title, string $content): array
    {
        $prompt = "Générez des hashtags pertinents pour cette publication. Retournez UNIQUEMENT un JSON valide :

Titre: {$title}
Contenu: {$content}

Format de réponse :
{
  \"hashtags\": [\"#hashtag1\", \"#hashtag2\", \"#hashtag3\"],
  \"trending_hashtags\": [\"#trend1\", \"#trend2\"],
  \"niche_hashtags\": [\"#niche1\", \"#niche2\"],
  \"branded_hashtags\": [\"#brand1\", \"#brand2\"]
}

Règles :
- Maximum 15 hashtags au total
- Mélangez hashtags populaires et de niche
- Incluez des hashtags en français
- Évitez les hashtags trop génériques";

        try {
            $response = $this->callOpenAI($prompt, 0.5);
            $hashtags = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $hashtags;
            }
        } catch (\Exception $e) {
            Log::warning('AI hashtag generation failed: ' . $e->getMessage());
        }

        // Fallback hashtag generation
        return $this->getFallbackHashtags($title, $content);
    }

    /**
     * Détection automatique de catégorie
     */
    private function detectCategory(string $title, string $content): array
    {
        $categories = [
            'technologie', 'environnement', 'recyclage', 'événement', 'éducation',
            'santé', 'sport', 'culture', 'actualités', 'lifestyle', 'business',
            'science', 'art', 'voyage', 'cuisine', 'mode', 'automobile'
        ];

        $prompt = "Classifiez cette publication dans les catégories appropriées. Retournez UNIQUEMENT un JSON valide :

Titre: {$title}
Contenu: {$content}

Catégories disponibles: " . implode(', ', $categories) . "

Format de réponse :
{
  \"primary_category\": \"catégorie principale\",
  \"secondary_categories\": [\"cat1\", \"cat2\"],
  \"confidence_score\": 0.95,
  \"reasoning\": \"Explication courte du choix\"
}";

        try {
            $response = $this->callOpenAI($prompt, 0.2);
            $category = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $category;
            }
        } catch (\Exception $e) {
            Log::warning('AI category detection failed: ' . $e->getMessage());
        }

        // Fallback category detection
        return $this->getFallbackCategory($title, $content);
    }

    /**
     * Suggestions d'amélioration du contenu
     */
    private function suggestContentImprovements(string $title, string $content): array
    {
        $prompt = "Analysez cette publication et suggérez des améliorations. Retournez UNIQUEMENT un JSON valide :

Titre: {$title}
Contenu: {$content}

Format de réponse :
{
  \"title_improvements\": {
    \"suggested_title\": \"Titre amélioré\",
    \"reasons\": [\"raison1\", \"raison2\"]
  },
  \"content_improvements\": {
    \"suggested_content\": \"Contenu amélioré\",
    \"improvements\": [\"amélioration1\", \"amélioration2\"]
  },
  \"structure_suggestions\": [\"suggestion1\", \"suggestion2\"],
  \"engagement_tips\": [\"tip1\", \"tip2\"],
  \"call_to_action\": \"Suggestion d'appel à l'action\"
}";

        try {
            $response = $this->callOpenAI($prompt, 0.4);
            $improvements = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $improvements;
            }
        } catch (\Exception $e) {
            Log::warning('AI content improvement failed: ' . $e->getMessage());
        }

        // Fallback improvements
        return $this->getFallbackImprovements($title, $content);
    }

    /**
     * Optimisation SEO automatique
     */
    private function generateSEOOptimization(string $title, string $content): array
    {
        $prompt = "Optimisez cette publication pour le SEO. Retournez UNIQUEMENT un JSON valide :

Titre: {$title}
Contenu: {$content}

Format de réponse :
{
  \"seo_title\": \"Titre optimisé SEO (50-60 caractères)\",
  \"meta_description\": \"Description meta (150-160 caractères)\",
  \"keywords\": [\"mot-clé1\", \"mot-clé2\", \"mot-clé3\"],
  \"long_tail_keywords\": [\"expression longue 1\", \"expression longue 2\"],
  \"internal_link_suggestions\": [\"suggestion1\", \"suggestion2\"],
  \"seo_score\": 8.5,
  \"optimization_tips\": [\"tip1\", \"tip2\"]
}";

        try {
            $response = $this->callOpenAI($prompt, 0.3);
            $seo = json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $seo;
            }
        } catch (\Exception $e) {
            Log::warning('AI SEO optimization failed: ' . $e->getMessage());
        }

        // Fallback SEO
        return $this->getFallbackSEO($title, $content);
    }

    /**
     * Analyse d'image avec IA
     */
    private function analyzeImage(string $imagePath, string $context): array
    {
        // Pour l'instant, analyse basique - peut être étendue avec Vision API
        return [
            'has_image' => true,
            'image_relevance' => 'high',
            'alt_text_suggestion' => $this->generateAltText($context),
            'image_seo_tips' => [
                'Ajoutez un texte alternatif descriptif',
                'Optimisez la taille de l\'image',
                'Utilisez un nom de fichier descriptif'
            ]
        ];
    }

    /**
     * Calcul du score de qualité global
     */
    private function calculateQualityScore(array $analysis): array
    {
        $scores = [];
        
        // Score basé sur l'analyse du contenu
        if (isset($analysis['content_analysis'])) {
            $content = $analysis['content_analysis'];
            $scores[] = $content['readability_score'] ?? 5;
            $scores[] = $content['engagement_potential'] ?? 5;
            $scores[] = $content['language_quality'] ?? 5;
        }
        
        // Score SEO
        if (isset($analysis['seo_optimization']['seo_score'])) {
            $scores[] = $analysis['seo_optimization']['seo_score'];
        }
        
        // Score de catégorisation
        if (isset($analysis['category']['confidence_score'])) {
            $scores[] = $analysis['category']['confidence_score'] * 10;
        }
        
        $averageScore = !empty($scores) ? array_sum($scores) / count($scores) : 5;
        
        return [
            'overall_score' => round($averageScore, 1),
            'grade' => $this->getQualityGrade($averageScore),
            'recommendations' => $this->getQualityRecommendations($averageScore),
            'score_breakdown' => $scores
        ];
    }

    /**
     * Appel à l'API OpenAI
     */
    private function callOpenAI(string $prompt, float $temperature = 0.7): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API key not configured');
        }

        $response = $this->httpClient->post($this->baseUrl . '/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => config('ai.openai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Vous êtes un assistant IA spécialisé dans l\'analyse et l\'amélioration de contenu pour les réseaux sociaux. Répondez toujours en JSON valide et en français.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => $temperature,
                'max_tokens' => 1500
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['choices'][0]['message']['content'] ?? '';
    }

    // Méthodes de fallback pour quand l'IA n'est pas disponible
    private function getFallbackContentAnalysis(string $title, string $content): array
    {
        return [
            'sentiment' => 'neutre',
            'tone' => 'informatif',
            'readability_score' => 7,
            'engagement_potential' => 6,
            'topics' => $this->extractTopicsFromText($title . ' ' . $content),
            'target_audience' => 'Audience générale',
            'language_quality' => 7,
            'suggestions' => ['Ajoutez plus de détails', 'Utilisez des émojis pour plus d\'engagement']
        ];
    }

    private function getFallbackHashtags(string $title, string $content): array
    {
        $text = strtolower($title . ' ' . $content);
        $hashtags = [];
        
        // Hashtags basés sur des mots-clés communs
        $keywords = ['environnement', 'recyclage', 'écologie', 'durable', 'vert', 'nature'];
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                $hashtags[] = '#' . $keyword;
            }
        }
        
        return [
            'hashtags' => array_slice($hashtags, 0, 5),
            'trending_hashtags' => ['#tendance', '#actualité'],
            'niche_hashtags' => ['#communauté', '#partage'],
            'branded_hashtags' => ['#monapp', '#publication']
        ];
    }

    private function getFallbackCategory(string $title, string $content): array
    {
        $text = strtolower($title . ' ' . $content);
        
        if (strpos($text, 'recyclage') !== false || strpos($text, 'environnement') !== false) {
            return [
                'primary_category' => 'environnement',
                'secondary_categories' => ['recyclage'],
                'confidence_score' => 0.8,
                'reasoning' => 'Détection basée sur les mots-clés environnementaux'
            ];
        }
        
        return [
            'primary_category' => 'général',
            'secondary_categories' => [],
            'confidence_score' => 0.5,
            'reasoning' => 'Catégorie par défaut'
        ];
    }

    private function getFallbackImprovements(string $title, string $content): array
    {
        return [
            'title_improvements' => [
                'suggested_title' => $title,
                'reasons' => ['Le titre actuel est acceptable']
            ],
            'content_improvements' => [
                'suggested_content' => $content,
                'improvements' => ['Ajoutez plus de détails', 'Structurez avec des paragraphes']
            ],
            'structure_suggestions' => ['Utilisez des listes à puces', 'Ajoutez des sous-titres'],
            'engagement_tips' => ['Posez une question', 'Ajoutez un appel à l\'action'],
            'call_to_action' => 'Qu\'en pensez-vous ? Partagez votre avis !'
        ];
    }

    private function getFallbackSEO(string $title, string $content): array
    {
        return [
            'seo_title' => substr($title, 0, 60),
            'meta_description' => substr($content, 0, 160),
            'keywords' => $this->extractTopicsFromText($title . ' ' . $content),
            'long_tail_keywords' => [],
            'internal_link_suggestions' => [],
            'seo_score' => 6.0,
            'optimization_tips' => ['Optimisez la longueur du titre', 'Ajoutez des mots-clés pertinents']
        ];
    }

    private function extractTopicsFromText(string $text): array
    {
        $commonWords = ['le', 'la', 'les', 'de', 'du', 'des', 'et', 'ou', 'à', 'dans', 'sur', 'avec', 'pour', 'par', 'un', 'une'];
        $words = str_word_count(strtolower($text), 1, 'àáâãäåæçèéêëìíîïñòóôõöøùúûüý');
        $words = array_diff($words, $commonWords);
        $words = array_filter($words, fn($word) => strlen($word) > 3);
        
        return array_slice(array_unique($words), 0, 5);
    }

    private function generateAltText(string $context): string
    {
        return 'Image illustrant: ' . substr($context, 0, 100) . '...';
    }

    private function getQualityGrade(float $score): string
    {
        if ($score >= 9) return 'Excellent';
        if ($score >= 8) return 'Très bon';
        if ($score >= 7) return 'Bon';
        if ($score >= 6) return 'Moyen';
        if ($score >= 5) return 'Passable';
        return 'À améliorer';
    }

    private function getQualityRecommendations(float $score): array
    {
        if ($score >= 8) {
            return ['Excellent contenu ! Continuez ainsi.'];
        } elseif ($score >= 6) {
            return ['Bon contenu, quelques améliorations possibles.', 'Ajoutez plus d\'engagement.'];
        } else {
            return [
                'Le contenu nécessite des améliorations importantes.',
                'Travaillez la structure et la clarté.',
                'Ajoutez plus de valeur pour vos lecteurs.'
            ];
        }
    }
}
