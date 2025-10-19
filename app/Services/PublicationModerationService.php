<?php

namespace App\Services;

use App\Models\User;

class PublicationModerationService
{
    private IntelligentModerationService $moderationService;
    private CommentToneService $toneService;

    public function __construct()
    {
        $this->moderationService = new IntelligentModerationService();
        $this->toneService = new CommentToneService();
    }

    /**
     * Analyse complète d'une publication (titre + contenu)
     */
    public function analyzePublication(string $title, string $content, ?User $user = null): array
    {
        // Analyser le titre et le contenu séparément
        $titleAnalysis = $this->moderationService->analyzeContent($title);
        $contentAnalysis = $this->moderationService->analyzeContent($content);
        
        // Analyser le ton global
        $combinedText = $title . ' ' . $content;
        $toneAnalysis = $this->toneService->analyze($combinedText);
        
        // Calculer le score global
        $globalScore = $this->calculateGlobalScore($titleAnalysis, $contentAnalysis, $user);
        
        // Déterminer l'action à prendre
        $action = $this->determineAction($globalScore, $user);
        
        return [
            'title_analysis' => $titleAnalysis,
            'content_analysis' => $contentAnalysis,
            'tone_analysis' => $toneAnalysis,
            'global_score' => $globalScore,
            'action' => $action,
            'is_approved' => $action['allow'],
            'needs_review' => $action['review'],
            'rejection_reason' => $action['reason'] ?? null,
            'suggestions' => $this->generatePublicationSuggestions($titleAnalysis, $contentAnalysis)
        ];
    }

    /**
     * Calcule le score global de la publication
     */
    private function calculateGlobalScore(array $titleAnalysis, array $contentAnalysis, ?User $user): int
    {
        // Score de base : moyenne pondérée (titre 30%, contenu 70%)
        $baseScore = ($titleAnalysis['severity_score'] * 0.3) + ($contentAnalysis['severity_score'] * 0.7);
        
        // Ajustements pour utilisateurs de confiance
        if ($user && $this->isTrustedUser($user)) {
            $baseScore *= 0.8; // Réduction de 20%
        }
        
        // Bonus/malus selon l'historique de l'utilisateur
        if ($user) {
            $userModifier = $this->getUserModerationModifier($user);
            $baseScore += $userModifier;
        }
        
        return min(100, max(0, (int)$baseScore));
    }

    /**
     * Détermine l'action à prendre selon le score
     */
    private function determineAction(int $score, ?User $user): array
    {
        $config = config('moderation.actions');
        $strictMode = config('moderation.strict_mode', false);
        
        // Ajuster les seuils selon le mode strict
        $thresholds = [
            'low' => $strictMode ? 30 : 40,
            'medium' => $strictMode ? 50 : 69,
            'high' => $strictMode ? 70 : 89
        ];
        
        if ($score <= $thresholds['low']) {
            return [
                'allow' => true,
                'review' => false,
                'level' => 'low',
                'message' => $config['low']['message']
            ];
        } elseif ($score <= $thresholds['medium']) {
            // Mode modéré : permettre mais marquer pour révision
            return [
                'allow' => !$strictMode,
                'review' => true,
                'level' => 'medium',
                'message' => $config['medium']['message'],
                'reason' => 'Contenu potentiellement inapproprié détecté'
            ];
        } elseif ($score <= $thresholds['high']) {
            return [
                'allow' => false,
                'review' => true,
                'level' => 'high',
                'message' => $config['high']['message'],
                'reason' => 'Contenu inapproprié détecté'
            ];
        } else {
            return [
                'allow' => false,
                'review' => false,
                'level' => 'critical',
                'message' => $config['critical']['message'],
                'reason' => 'Violation grave des conditions d\'utilisation'
            ];
        }
    }

    /**
     * Vérifie si l'utilisateur est de confiance
     */
    private function isTrustedUser(User $user): bool
    {
        if (!config('moderation.trusted_users.enabled', true)) {
            return false;
        }
        
        $trustedRoles = config('moderation.trusted_users.roles', []);
        
        // Vérifier le rôle
        if (method_exists($user, 'hasRole')) {
            foreach ($trustedRoles as $role) {
                if ($user->hasRole($role)) {
                    return true;
                }
            }
        }
        
        // Vérifier d'autres critères (ancienneté, réputation, etc.)
        return $this->checkUserReputation($user);
    }

    /**
     * Vérifie la réputation de l'utilisateur
     */
    private function checkUserReputation(User $user): bool
    {
        // Critères de réputation
        $accountAge = $user->created_at->diffInDays(now());
        $publicationCount = $user->publications()->count();
        $approvedPublications = $user->publications()->where('is_approved', true)->count();
        
        // Utilisateur de confiance si :
        // - Compte > 30 jours
        // - Plus de 5 publications
        // - Taux d'approbation > 80%
        if ($accountAge > 30 && $publicationCount > 5) {
            $approvalRate = $publicationCount > 0 ? ($approvedPublications / $publicationCount) : 0;
            return $approvalRate > 0.8;
        }
        
        return false;
    }

    /**
     * Calcule le modificateur basé sur l'historique de modération
     */
    private function getUserModerationModifier(User $user): int
    {
        // Récupérer l'historique récent (30 derniers jours)
        $recentPublications = $user->publications()
            ->where('created_at', '>=', now()->subDays(30))
            ->get();
        
        if ($recentPublications->isEmpty()) {
            return 0;
        }
        
        $rejectedCount = $recentPublications->where('is_approved', false)->count();
        $totalCount = $recentPublications->count();
        $rejectionRate = $rejectedCount / $totalCount;
        
        // Pénalité si taux de rejet élevé
        if ($rejectionRate > 0.3) {
            return 15; // Augmente le score de sévérité
        } elseif ($rejectionRate < 0.1) {
            return -5; // Bonus pour bon comportement
        }
        
        return 0;
    }

    /**
     * Génère des suggestions spécifiques aux publications
     */
    private function generatePublicationSuggestions(array $titleAnalysis, array $contentAnalysis): array
    {
        $suggestions = [];
        
        // Suggestions pour le titre
        if ($titleAnalysis['severity_score'] > 50) {
            $suggestions['title'] = [
                'message' => 'Votre titre contient du langage inapproprié.',
                'recommendations' => [
                    'Utilisez un titre plus neutre et descriptif',
                    'Évitez les termes provocateurs',
                    'Concentrez-vous sur le contenu principal'
                ]
            ];
        }
        
        // Suggestions pour le contenu
        if ($contentAnalysis['severity_score'] > 50) {
            $suggestions['content'] = [
                'message' => 'Votre contenu nécessite des améliorations.',
                'recommendations' => []
            ];
            
            // Recommandations spécifiques selon les catégories détectées
            foreach ($contentAnalysis['detected_words'] as $word) {
                switch ($word['category']) {
                    case 'profanity':
                        $suggestions['content']['recommendations'][] = 'Remplacez le langage vulgaire par des expressions plus appropriées';
                        break;
                    case 'hate_speech':
                        $suggestions['content']['recommendations'][] = 'Adoptez un ton plus respectueux envers tous les groupes';
                        break;
                    case 'harassment':
                        $suggestions['content']['recommendations'][] = 'Évitez les attaques personnelles ou les menaces';
                        break;
                }
            }
        }
        
        // Suggestions générales
        if (!empty($titleAnalysis['suggestions']) || !empty($contentAnalysis['suggestions'])) {
            $suggestions['general'] = [
                'message' => 'Suggestions de remplacement disponibles',
                'title_suggestions' => $titleAnalysis['suggestions'] ?? [],
                'content_suggestions' => $contentAnalysis['suggestions'] ?? []
            ];
        }
        
        return $suggestions;
    }

    /**
     * Analyse rapide pour l'aperçu en temps réel
     */
    public function quickAnalyze(string $text): array
    {
        $analysis = $this->moderationService->analyzeContent($text);
        
        return [
            'score' => $analysis['severity_score'],
            'is_safe' => $analysis['severity_score'] < 70,
            'warning_level' => $this->getWarningLevel($analysis['severity_score']),
            'detected_categories' => array_unique(array_column($analysis['detected_words'], 'category'))
        ];
    }

    /**
     * Détermine le niveau d'avertissement
     */
    private function getWarningLevel(int $score): string
    {
        if ($score < 30) return 'safe';
        if ($score < 50) return 'caution';
        if ($score < 70) return 'warning';
        return 'danger';
    }

    /**
     * Obtient les statistiques de modération pour un utilisateur
     */
    public function getUserModerationStats(User $user): array
    {
        $publications = $user->publications()->get();
        $comments = $user->commentaires()->get();
        
        return [
            'publications' => [
                'total' => $publications->count(),
                'approved' => $publications->where('is_approved', true)->count(),
                'pending' => $publications->where('is_approved', false)->count(),
                'approval_rate' => $publications->count() > 0 ? 
                    ($publications->where('is_approved', true)->count() / $publications->count()) * 100 : 0
            ],
            'comments' => [
                'total' => $comments->count(),
                'with_bad_words' => $comments->where('has_bad_words', true)->count(),
                'negative_tone' => $comments->where('tone', 'NEGATIF')->count()
            ],
            'reputation' => [
                'is_trusted' => $this->isTrustedUser($user),
                'account_age_days' => $user->created_at->diffInDays(now()),
                'moderation_modifier' => $this->getUserModerationModifier($user)
            ]
        ];
    }
}
