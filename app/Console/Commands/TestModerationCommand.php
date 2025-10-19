<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IntelligentModerationService;
use App\Services\CommentToneService;

class TestModerationCommand extends Command
{
    protected $signature = 'moderation:test {--interactive : Mode interactif pour tester vos propres textes}';
    protected $description = 'Test du système de modération intelligent';

    private IntelligentModerationService $moderationService;
    private CommentToneService $toneService;

    public function __construct()
    {
        parent::__construct();
        $this->moderationService = new IntelligentModerationService();
        $this->toneService = new CommentToneService();
    }

    public function handle()
    {
        $this->info('🔍 Test du système de modération intelligent');
        $this->line('');

        if ($this->option('interactive')) {
            $this->runInteractiveMode();
        } else {
            $this->runTestSuite();
        }
    }

    private function runInteractiveMode()
    {
        $this->info('Mode interactif activé. Tapez "quit" pour quitter.');
        $this->line('');

        while (true) {
            $text = $this->ask('Entrez un texte à analyser');
            
            if (strtolower($text) === 'quit') {
                $this->info('Au revoir !');
                break;
            }

            if (empty($text)) {
                $this->warn('Veuillez entrer un texte.');
                continue;
            }

            $this->analyzeText($text);
            $this->line('');
        }
    }

    private function runTestSuite()
    {
        $testCases = [
            // Cas normaux
            'Bonjour, j\'aimerais partager cette belle photo de mon jardin.',
            'Super événement hier soir ! Merci à tous les participants.',
            
            // Cas avec mots inappropriés basiques
            'Tu es vraiment con de penser ça.',
            'Merde, j\'ai encore oublié mes clés !',
            
            // Cas avec contournements l33t speak
            'Tu es un vrai c0nn4rd !',
            'M3rd3 alors, ça marche pas !',
            
            // Cas avec espaces et caractères
            'Tu es un c o n n a r d',
            'M-e-r-d-e de situation',
            
            // Cas avec répétitions
            'Connnnnnard !',
            'Meeeeerde !',
            
            // Cas avec contexte agressif
            'VA TE FAIRE VOIR !!! Tu me dégoûtes vraiment.',
            'Je vais te montrer qui commande ici, salaud !',
            
            // Cas limites
            'Console de jeu en panne',  // Contient "con" mais contexte technique
            'Connection internet lente', // Contient "con" mais contexte technique
            
            // Cas de harcèlement
            'Arrête de me suivre partout, laisse moi tranquille !',
            'Tu vas voir ce qui va t\'arriver',
            
            // Spam indicators
            'URGENT !!! Cliquez ici pour gagner 1000€ GRATUIT !!!',
            'Promo exceptionnelle aaaaaaaaaaaaa'
        ];

        $this->info('Exécution de la suite de tests...');
        $this->line('');

        foreach ($testCases as $index => $text) {
            $this->info("Test " . ($index + 1) . ": " . substr($text, 0, 50) . (strlen($text) > 50 ? '...' : ''));
            $this->analyzeText($text);
            $this->line('');
        }

        // Statistiques du service
        $this->displayModerationStats();
    }

    private function analyzeText(string $text)
    {
        // Analyse avec le service intelligent
        $moderationResult = $this->moderationService->analyzeContent($text);
        
        // Analyse du ton
        $toneResult = $this->toneService->analyze($text);

        // Affichage des résultats
        $this->displayResults($text, $moderationResult, $toneResult);
    }

    private function displayResults(string $text, array $moderationResult, array $toneResult)
    {
        // Couleurs selon le niveau de sévérité
        $scoreColor = $this->getScoreColor($moderationResult['severity_score']);
        
        $this->line("📝 <comment>Texte:</comment> {$text}");
        $this->line("📊 <comment>Score de sévérité:</comment> <{$scoreColor}>{$moderationResult['severity_score']}/100</{$scoreColor}>");
        $this->line("🎭 <comment>Ton:</comment> " . $this->getToneEmoji($toneResult['tone']) . " {$toneResult['tone']}");
        $this->line("🚫 <comment>Inapproprié:</comment> " . ($moderationResult['is_inappropriate'] ? '❌ Oui' : '✅ Non'));
        $this->line("🎯 <comment>Confiance:</comment> {$moderationResult['confidence']}%");

        if (!empty($moderationResult['detected_words'])) {
            $this->line("🔍 <comment>Mots détectés:</comment>");
            foreach ($moderationResult['detected_words'] as $word) {
                $this->line("   • {$word['word']} (catégorie: {$word['category']}, sévérité: {$word['severity']})");
            }
        }

        if (!empty($moderationResult['context_analysis'])) {
            $contextScores = array_filter($moderationResult['context_analysis'], fn($score) => $score > 0);
            if (!empty($contextScores)) {
                $this->line("🧠 <comment>Analyse contextuelle:</comment>");
                foreach ($contextScores as $category => $score) {
                    $this->line("   • {$category}: {$score}");
                }
            }
        }

        if (!empty($moderationResult['suggestions'])) {
            $this->line("💡 <comment>Suggestions:</comment>");
            foreach ($moderationResult['suggestions'] as $suggestion) {
                $this->line("   • {$suggestion['message']}");
                if (!empty($suggestion['alternatives'])) {
                    $this->line("     Alternatives: " . implode(', ', $suggestion['alternatives']));
                }
            }
        }

        // Action recommandée
        $action = $this->getRecommendedAction($moderationResult['severity_score']);
        $actionColor = $this->getActionColor($action);
        $this->line("⚡ <comment>Action recommandée:</comment> <{$actionColor}>{$action}</{$actionColor}>");
    }

    private function getScoreColor(int $score): string
    {
        if ($score < 30) return 'info';
        if ($score < 50) return 'comment';
        if ($score < 70) return 'question';
        return 'error';
    }

    private function getToneEmoji(string $tone): string
    {
        return match($tone) {
            'POSITIF' => '😊',
            'NEGATIF' => '😠',
            'NEUTRE' => '😐',
            default => '❓'
        };
    }

    private function getRecommendedAction(int $score): string
    {
        if ($score < 30) return 'APPROUVER';
        if ($score < 50) return 'AVERTIR';
        if ($score < 70) return 'RÉVISER';
        return 'BLOQUER';
    }

    private function getActionColor(string $action): string
    {
        return match($action) {
            'APPROUVER' => 'info',
            'AVERTIR' => 'comment',
            'RÉVISER' => 'question',
            'BLOQUER' => 'error',
            default => 'comment'
        };
    }

    private function displayModerationStats()
    {
        $stats = $this->moderationService->getModerationStats();
        
        $this->info('📈 Statistiques du système de modération:');
        $this->line("   • Patterns totaux: {$stats['total_patterns']}");
        $this->line("   • Catégories: " . implode(', ', $stats['categories']));
        $this->line("   • Seuil de sévérité: {$stats['severity_threshold']}");
        $this->line("   • Règles contextuelles: {$stats['contextual_rules']}");
    }
}
