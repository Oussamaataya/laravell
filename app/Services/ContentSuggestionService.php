<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContentSuggestionService
{
    private PublicationAIService $aiService;

    public function __construct()
    {
        $this->aiService = new PublicationAIService();
    }

    /**
     * Génère des suggestions de contenu personnalisées pour un utilisateur
     */
    public function getPersonalizedSuggestions(User $user): array
    {
        $cacheKey = "content_suggestions_user_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return [
                'trending_topics' => $this->getTrendingTopics(),
                'personalized_ideas' => $this->getPersonalizedIdeas($user),
                'template_suggestions' => $this->getTemplateSuggestions(),
                'hashtag_trends' => $this->getHashtagTrends(),
                'content_calendar' => $this->generateContentCalendar($user),
                'inspiration_prompts' => $this->getInspirationPrompts()
            ];
        });
    }

    /**
     * Analyse les tendances actuelles
     */
    public function getTrendingTopics(): array
    {
        // Analyse des publications récentes pour détecter les tendances
        $recentPublications = Publication::where('created_at', '>=', now()->subDays(7))
            ->where('is_approved', true)
            ->get();

        $topics = [];
        $hashtags = [];

        foreach ($recentPublications as $publication) {
            // Extraire les sujets du contenu
            $content = $publication->titre . ' ' . $publication->contenu;
            $extractedTopics = $this->extractTopicsFromText($content);
            
            foreach ($extractedTopics as $topic) {
                $topics[$topic] = ($topics[$topic] ?? 0) + 1;
            }

            // Extraire les hashtags
            if ($publication->ai_hashtags) {
                foreach ($publication->ai_hashtags as $hashtag) {
                    $hashtags[$hashtag] = ($hashtags[$hashtag] ?? 0) + 1;
                }
            }
        }

        // Trier par popularité
        arsort($topics);
        arsort($hashtags);

        return [
            'hot_topics' => array_slice(array_keys($topics), 0, 10),
            'trending_hashtags' => array_slice(array_keys($hashtags), 0, 15),
            'topic_scores' => array_slice($topics, 0, 10),
            'seasonal_trends' => $this->getSeasonalTrends(),
            'weekly_growth' => $this->calculateWeeklyGrowth($topics)
        ];
    }

    /**
     * Génère des idées personnalisées basées sur l'historique de l'utilisateur
     */
    private function getPersonalizedIdeas(User $user): array
    {
        $userPublications = $user->publications()
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $userTopics = [];
        $userStyle = $this->analyzeUserStyle($userPublications);

        foreach ($userPublications as $publication) {
            $content = $publication->titre . ' ' . $publication->contenu;
            $topics = $this->extractTopicsFromText($content);
            
            foreach ($topics as $topic) {
                $userTopics[$topic] = ($userTopics[$topic] ?? 0) + 1;
            }
        }

        return [
            'continuation_ideas' => $this->generateContinuationIdeas($userTopics),
            'style_based_suggestions' => $this->getStyleBasedSuggestions($userStyle),
            'engagement_boosters' => $this->getEngagementBoosters($user),
            'content_gaps' => $this->identifyContentGaps($userTopics),
            'collaboration_opportunities' => $this->findCollaborationOpportunities($user)
        ];
    }

    /**
     * Fournit des modèles de contenu prêts à utiliser
     */
    private function getTemplateSuggestions(): array
    {
        return [
            'question_posts' => [
                'Quelle est votre astuce préférée pour [SUJET] ?',
                'Comment gérez-vous [DÉFI] dans votre quotidien ?',
                'Quel est votre avis sur [TENDANCE ACTUELLE] ?'
            ],
            'educational_posts' => [
                '5 choses que vous ne saviez pas sur [SUJET]',
                'Le guide complet pour débuter en [DOMAINE]',
                'Erreurs courantes à éviter en [ACTIVITÉ]'
            ],
            'behind_scenes' => [
                'Dans les coulisses de [PROJET]',
                'Mon processus créatif pour [CRÉATION]',
                'Une journée type dans ma vie de [PROFESSION]'
            ],
            'storytelling' => [
                'L\'histoire derrière [RÉALISATION]',
                'Comment j\'ai surmonté [DÉFI]',
                'La leçon que m\'a apprise [EXPÉRIENCE]'
            ],
            'seasonal_content' => $this->getSeasonalTemplates()
        ];
    }

    /**
     * Analyse les tendances de hashtags
     */
    private function getHashtagTrends(): array
    {
        $hashtagData = DB::table('publications')
            ->whereNotNull('ai_hashtags')
            ->where('created_at', '>=', now()->subDays(30))
            ->where('is_approved', true)
            ->get()
            ->pluck('ai_hashtags')
            ->flatten()
            ->countBy()
            ->sortDesc()
            ->take(20);

        return [
            'rising_hashtags' => $this->identifyRisingHashtags(),
            'evergreen_hashtags' => $this->getEvergreenHashtags(),
            'niche_hashtags' => $this->getNicheHashtags(),
            'hashtag_combinations' => $this->suggestHashtagCombinations(),
            'performance_data' => $hashtagData->toArray()
        ];
    }

    /**
     * Génère un calendrier de contenu
     */
    private function generateContentCalendar(User $user): array
    {
        $calendar = [];
        $today = now();

        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            $dayType = $this->getDayType($date);
            
            $calendar[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'),
                'suggested_content_type' => $this->getSuggestedContentType($dayType),
                'optimal_posting_time' => $this->getOptimalPostingTime($date),
                'content_ideas' => $this->getContentIdeasForDay($dayType),
                'hashtag_suggestions' => $this->getHashtagsForDay($dayType)
            ];
        }

        return $calendar;
    }

    /**
     * Fournit des prompts d'inspiration
     */
    private function getInspirationPrompts(): array
    {
        return [
            'creative_prompts' => [
                'Si vous pouviez changer une chose dans votre domaine, que serait-ce ?',
                'Quel conseil donneriez-vous à votre moi d\'il y a 5 ans ?',
                'Quelle innovation récente vous inspire le plus ?'
            ],
            'reflection_prompts' => [
                'Quelle a été votre plus grande réussite cette semaine ?',
                'Quel défi vous a le plus fait grandir récemment ?',
                'Qu\'avez-vous appris de nouveau aujourd\'hui ?'
            ],
            'engagement_prompts' => [
                'Partagez une photo qui résume votre humeur du jour',
                'Décrivez votre espace de travail idéal en 3 mots',
                'Quel livre/podcast/article recommanderiez-vous ?'
            ],
            'trending_prompts' => $this->getTrendingPrompts()
        ];
    }

    /**
     * Analyse le style d'écriture de l'utilisateur
     */
    private function analyzeUserStyle(iterable $publications): array
    {
        $totalLength = 0;
        $totalPosts = 0;
        $tones = [];
        $topics = [];

        foreach ($publications as $publication) {
            $content = $publication->contenu;
            $totalLength += strlen($content);
            $totalPosts++;

            // Analyser le ton
            $tone = $this->detectTone($content);
            $tones[$tone] = ($tones[$tone] ?? 0) + 1;

            // Analyser les sujets
            $postTopics = $this->extractTopicsFromText($content);
            foreach ($postTopics as $topic) {
                $topics[$topic] = ($topics[$topic] ?? 0) + 1;
            }
        }

        $avgLength = $totalPosts > 0 ? $totalLength / $totalPosts : 0;
        arsort($tones);
        arsort($topics);

        return [
            'average_length' => $avgLength,
            'preferred_tone' => array_key_first($tones) ?? 'informatif',
            'favorite_topics' => array_slice(array_keys($topics), 0, 5),
            'writing_style' => $this->determineWritingStyle($avgLength, $tones),
            'posting_frequency' => $this->calculatePostingFrequency($publications)
        ];
    }

    /**
     * Génère des idées de continuation basées sur les sujets favoris
     */
    private function generateContinuationIdeas(array $userTopics): array
    {
        $ideas = [];
        
        foreach (array_slice($userTopics, 0, 3, true) as $topic => $count) {
            $ideas[] = [
                'topic' => $topic,
                'ideas' => [
                    "Approfondissez votre expertise en {$topic}",
                    "Partagez une astuce avancée sur {$topic}",
                    "Répondez aux questions fréquentes sur {$topic}",
                    "Montrez l'évolution de vos connaissances en {$topic}"
                ]
            ];
        }

        return $ideas;
    }

    /**
     * Identifie les lacunes dans le contenu de l'utilisateur
     */
    private function identifyContentGaps(array $userTopics): array
    {
        $allTrendingTopics = $this->getTrendingTopics()['hot_topics'];
        $gaps = array_diff($allTrendingTopics, array_keys($userTopics));

        return [
            'missing_trending_topics' => array_slice($gaps, 0, 5),
            'underexplored_areas' => $this->getUnderexploredAreas($userTopics),
            'content_diversification' => $this->suggestContentDiversification($userTopics)
        ];
    }

    // Méthodes utilitaires
    private function extractTopicsFromText(string $text): array
    {
        $commonWords = ['le', 'la', 'les', 'de', 'du', 'des', 'et', 'ou', 'à', 'dans', 'sur', 'avec', 'pour', 'par'];
        $words = str_word_count(strtolower($text), 1, 'àáâãäåæçèéêëìíîïñòóôõöøùúûüý');
        $words = array_diff($words, $commonWords);
        return array_filter($words, fn($word) => strlen($word) > 3);
    }

    private function getSeasonalTrends(): array
    {
        $month = now()->month;
        
        $seasonal = [
            1 => ['nouvelles-résolutions', 'détox-numérique', 'organisation'],
            2 => ['saint-valentin', 'amour-propre', 'relations'],
            3 => ['printemps', 'renouveau', 'jardinage'],
            4 => ['pâques', 'famille', 'traditions'],
            5 => ['mai', 'nature', 'écologie'],
            6 => ['été', 'vacances', 'voyage'],
            7 => ['été', 'détente', 'loisirs'],
            8 => ['vacances', 'repos', 'lecture'],
            9 => ['rentrée', 'nouveaux-projets', 'apprentissage'],
            10 => ['automne', 'halloween', 'changement'],
            11 => ['gratitude', 'thanksgiving', 'reconnaissance'],
            12 => ['noël', 'bilan-année', 'famille']
        ];

        return $seasonal[$month] ?? ['général', 'quotidien', 'inspiration'];
    }

    private function detectTone(string $content): string
    {
        $content = strtolower($content);
        
        if (strpos($content, '?') !== false) return 'interrogatif';
        if (strpos($content, '!') !== false) return 'enthousiaste';
        if (preg_match('/\b(conseil|astuce|guide|comment)\b/', $content)) return 'informatif';
        if (preg_match('/\b(merci|génial|super|excellent)\b/', $content)) return 'positif';
        
        return 'neutre';
    }

    private function determineWritingStyle(float $avgLength, array $tones): string
    {
        if ($avgLength > 500) return 'détaillé';
        if ($avgLength > 200) return 'modéré';
        return 'concis';
    }

    private function calculatePostingFrequency($publications): string
    {
        if ($publications->isEmpty()) return 'nouveau';
        
        $daysSinceFirst = $publications->last()->created_at->diffInDays(now());
        $postsCount = $publications->count();
        
        if ($daysSinceFirst == 0) return 'nouveau';
        
        $avgDaysBetweenPosts = $daysSinceFirst / $postsCount;
        
        if ($avgDaysBetweenPosts <= 1) return 'quotidien';
        if ($avgDaysBetweenPosts <= 3) return 'régulier';
        if ($avgDaysBetweenPosts <= 7) return 'hebdomadaire';
        
        return 'occasionnel';
    }

    private function getDayType(\DateTime $date): string
    {
        $dayOfWeek = $date->format('N'); // 1 = lundi, 7 = dimanche
        
        if ($dayOfWeek == 1) return 'monday-motivation';
        if ($dayOfWeek == 3) return 'wisdom-wednesday';
        if ($dayOfWeek == 5) return 'feature-friday';
        if ($dayOfWeek >= 6) return 'weekend-vibes';
        
        return 'regular-day';
    }

    private function getSuggestedContentType(string $dayType): string
    {
        $types = [
            'monday-motivation' => 'Contenu motivationnel',
            'wisdom-wednesday' => 'Partage de connaissances',
            'feature-friday' => 'Mise en avant de projets',
            'weekend-vibes' => 'Contenu décontracté',
            'regular-day' => 'Contenu informatif'
        ];

        return $types[$dayType] ?? 'Contenu général';
    }

    private function getOptimalPostingTime(\DateTime $date): string
    {
        $dayOfWeek = $date->format('N');
        
        // Heures optimales basées sur les statistiques d'engagement
        if ($dayOfWeek <= 5) { // Jours de semaine
            return '12:00'; // Pause déjeuner
        } else { // Weekend
            return '10:00'; // Matinée weekend
        }
    }

    private function getContentIdeasForDay(string $dayType): array
    {
        $ideas = [
            'monday-motivation' => [
                'Citation inspirante de la semaine',
                'Objectifs pour la nouvelle semaine',
                'Success story motivante'
            ],
            'wisdom-wednesday' => [
                'Astuce professionnelle',
                'Leçon apprise récemment',
                'Partage d\'expertise'
            ],
            'feature-friday' => [
                'Projet de la semaine',
                'Collaboration mise en avant',
                'Réalisation personnelle'
            ],
            'weekend-vibes' => [
                'Moment de détente',
                'Activité weekend',
                'Réflexion personnelle'
            ]
        ];

        return $ideas[$dayType] ?? ['Contenu général', 'Partage d\'expérience', 'Question à la communauté'];
    }

    private function getHashtagsForDay(string $dayType): array
    {
        $hashtags = [
            'monday-motivation' => ['#MondayMotivation', '#NouvellesSemaine', '#Motivation'],
            'wisdom-wednesday' => ['#WisdomWednesday', '#Apprentissage', '#Conseil'],
            'feature-friday' => ['#FeatureFriday', '#Projet', '#Réalisation'],
            'weekend-vibes' => ['#Weekend', '#Détente', '#Inspiration']
        ];

        return $hashtags[$dayType] ?? ['#Partage', '#Communauté', '#Inspiration'];
    }

    private function identifyRisingHashtags(): array
    {
        // Logique pour identifier les hashtags en croissance
        return ['#TendanceEmergente', '#NouveauTrend', '#Innovation'];
    }

    private function getEvergreenHashtags(): array
    {
        return ['#Inspiration', '#Motivation', '#Apprentissage', '#Communauté', '#Partage'];
    }

    private function getNicheHashtags(): array
    {
        return ['#ExpertConseil', '#NicheSpecialiste', '#CommunauteExperte'];
    }

    private function suggestHashtagCombinations(): array
    {
        return [
            'engagement_boost' => ['#Question', '#VotreAvis', '#Communauté'],
            'educational' => ['#Conseil', '#Astuce', '#Apprentissage'],
            'personal' => ['#MonExpérience', '#Partage', '#Authentique']
        ];
    }

    private function getTrendingPrompts(): array
    {
        return [
            'Quel est le défi principal de votre secteur en 2024 ?',
            'Comment l\'IA change-t-elle votre façon de travailler ?',
            'Quelle habitude avez-vous adoptée récemment ?'
        ];
    }

    private function getStyleBasedSuggestions(array $userStyle): array
    {
        $suggestions = [];
        
        if ($userStyle['writing_style'] === 'concis') {
            $suggestions[] = 'Essayez d\'ajouter plus de détails pour enrichir vos posts';
        }
        
        if ($userStyle['preferred_tone'] === 'informatif') {
            $suggestions[] = 'Variez avec du contenu plus personnel ou émotionnel';
        }

        return $suggestions;
    }

    private function getEngagementBoosters(User $user): array
    {
        return [
            'Posez plus de questions à votre audience',
            'Partagez des histoires personnelles',
            'Utilisez des sondages et des quiz',
            'Répondez rapidement aux commentaires',
            'Collaborez avec d\'autres créateurs'
        ];
    }

    private function findCollaborationOpportunities(User $user): array
    {
        // Logique pour trouver des opportunités de collaboration
        return [
            'Utilisateurs avec des intérêts similaires',
            'Experts dans des domaines complémentaires',
            'Créateurs avec une audience similaire'
        ];
    }

    private function getUnderexploredAreas(array $userTopics): array
    {
        $allAreas = ['technologie', 'environnement', 'santé', 'éducation', 'culture', 'sport'];
        return array_diff($allAreas, array_keys($userTopics));
    }

    private function suggestContentDiversification(array $userTopics): array
    {
        return [
            'Explorez de nouveaux formats (vidéo, infographie)',
            'Abordez des sujets connexes à vos thèmes favoris',
            'Invitez des experts pour diversifier les perspectives'
        ];
    }

    private function getSeasonalTemplates(): array
    {
        $month = now()->month;
        
        if (in_array($month, [12, 1, 2])) {
            return ['Bilan de l\'année', 'Résolutions', 'Nouveaux départs'];
        } elseif (in_array($month, [3, 4, 5])) {
            return ['Renouveau printanier', 'Nouveaux projets', 'Croissance'];
        } elseif (in_array($month, [6, 7, 8])) {
            return ['Projets d\'été', 'Détente', 'Voyages et découvertes'];
        } else {
            return ['Préparation automne', 'Bilan mi-parcours', 'Nouveaux apprentissages'];
        }
    }

    private function calculateWeeklyGrowth(array $topics): array
    {
        // Logique pour calculer la croissance hebdomadaire des sujets
        $growth = [];
        foreach (array_slice($topics, 0, 5, true) as $topic => $count) {
            $growth[$topic] = rand(5, 25) . '%'; // Simulation - à remplacer par vraie logique
        }
        return $growth;
    }
}
