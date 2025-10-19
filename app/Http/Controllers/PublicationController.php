<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Services\ImageEventService;
use App\Services\PublicationModerationService;
use App\Services\PublicationAIService;
use App\Services\ContentSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publications = Publication::where('is_approved', true)
            ->with(['user', 'commentaires', 'likes'])
            ->latest()
            ->paginate(10);
        
        return view('publications.index', compact('publications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtenir les suggestions de contenu pour l'utilisateur connecté
        $suggestionService = new ContentSuggestionService();
        $suggestions = $suggestionService->getPersonalizedSuggestions(Auth::user());
        
        return view('publications.create', compact('suggestions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Analyser le contenu avec le service de modération intelligent
        $moderationService = new PublicationModerationService();
        $moderationResult = $moderationService->analyzePublication(
            $request->input('titre'),
            $request->input('contenu'),
            Auth::user()
        );

        // Vérifier si la publication peut être approuvée
        if (!$moderationResult['is_approved']) {
            $message = $moderationResult['rejection_reason'] ?? 'Votre publication contient du contenu inapproprié.';
            
            // Ajouter les suggestions si disponibles
            if (!empty($moderationResult['suggestions'])) {
                $message .= ' Suggestions d\'amélioration disponibles.';
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $message)
                ->with('moderation_result', $moderationResult);
        }

        $data = $request->only(['titre', 'contenu']);
        $data['user_id'] = Auth::id();
        $data['is_approved'] = $moderationResult['is_approved'];
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('publications', 'public');
            $data['image'] = $imagePath;

            // Analyze image to extract event info (only for publications)
            $service = new ImageEventService();
            $ai = $service->analyze($imagePath, $request->input('contenu'));
            $data['event_type'] = $ai['event_type'];
            $data['ai_description'] = $ai['description'];
            $data['ai_hashtags'] = $ai['hashtags'];
        }

        $publication = Publication::create($data);

        $successMessage = 'Publication créée avec succès!';
        if ($moderationResult['needs_review']) {
            $successMessage .= ' Elle sera examinée par nos modérateurs avant publication.';
        }

        return redirect()->route('publications.index')
            ->with('success', $successMessage);
    }

    /**
     * Analyze an image filename (AJAX) and return AI suggestions.
     */
    public function analyzeImage(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'contenu' => 'nullable|string'
        ]);

        $service = new ImageEventService();
        $result = $service->analyze($request->input('filename'), $request->input('contenu'));

        return response()->json($result);
    }

    /**
     * Analyse en temps réel du contenu (AJAX)
     */
    public function analyzeContent(Request $request)
    {
        $request->validate([
            'titre' => 'nullable|string',
            'contenu' => 'nullable|string'
        ]);

        $moderationService = new PublicationModerationService();
        
        // Analyser le texte combiné ou séparément
        $text = trim(($request->input('titre') ?? '') . ' ' . ($request->input('contenu') ?? ''));
        
        if (empty($text)) {
            return response()->json([
                'score' => 0,
                'is_safe' => true,
                'warning_level' => 'safe',
                'message' => null
            ]);
        }

        $result = $moderationService->quickAnalyze($text);
        
        // Ajouter un message contextuel
        $messages = [
            'safe' => null,
            'caution' => 'Attention : votre contenu pourrait être considéré comme inapproprié.',
            'warning' => 'Votre contenu contient des éléments problématiques.',
            'danger' => 'Votre contenu ne peut pas être publié en l\'état.'
        ];
        
        $result['message'] = $messages[$result['warning_level']];
        
        return response()->json($result);
    }

    /**
     * Analyse complète IA d'une publication (AJAX)
     */
    public function analyzeWithAI(Request $request)
    {
        $request->validate([
            'titre' => 'required|string',
            'contenu' => 'required|string',
            'image_path' => 'nullable|string'
        ]);

        try {
            $aiService = new PublicationAIService();
            $analysis = $aiService->analyzePublication(
                $request->input('titre'),
                $request->input('contenu'),
                $request->input('image_path')
            );

            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Analyse IA temporairement indisponible',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère des suggestions de contenu (AJAX)
     */
    public function generateContentSuggestions(Request $request)
    {
        $request->validate([
            'topic' => 'nullable|string',
            'style' => 'nullable|string',
            'length' => 'nullable|string'
        ]);

        try {
            $suggestionService = new ContentSuggestionService();
            $suggestions = $suggestionService->getPersonalizedSuggestions(Auth::user());
            
            // Filtrer selon les paramètres
            $filteredSuggestions = $this->filterSuggestions($suggestions, $request->all());

            return response()->json([
                'success' => true,
                'suggestions' => $filteredSuggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Génération de suggestions échouée'
            ], 500);
        }
    }

    /**
     * Améliore automatiquement le contenu avec l'IA (AJAX)
     */
    public function improveContent(Request $request)
    {
        $request->validate([
            'titre' => 'required|string',
            'contenu' => 'required|string',
            'improvement_type' => 'required|string|in:grammar,style,engagement,seo'
        ]);

        try {
            $aiService = new PublicationAIService();
            $analysis = $aiService->analyzePublication(
                $request->input('titre'),
                $request->input('contenu')
            );

            $improvements = $analysis['content_improvements'] ?? [];
            $improvementType = $request->input('improvement_type');

            $result = $this->getSpecificImprovement($improvements, $improvementType);

            return response()->json([
                'success' => true,
                'improved_content' => $result,
                'original_analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Amélioration du contenu échouée'
            ], 500);
        }
    }

    /**
     * Génère des hashtags intelligents (AJAX)
     */
    public function generateHashtags(Request $request)
    {
        $request->validate([
            'titre' => 'required|string',
            'contenu' => 'required|string',
            'category' => 'nullable|string'
        ]);

        try {
            $aiService = new PublicationAIService();
            $analysis = $aiService->analyzePublication(
                $request->input('titre'),
                $request->input('contenu')
            );

            $hashtags = $analysis['hashtags'] ?? [];

            return response()->json([
                'success' => true,
                'hashtags' => $hashtags,
                'category' => $analysis['category'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Génération de hashtags échouée'
            ], 500);
        }
    }

    /**
     * Obtient le calendrier de contenu personnalisé
     */
    public function getContentCalendar()
    {
        try {
            $suggestionService = new ContentSuggestionService();
            $suggestions = $suggestionService->getPersonalizedSuggestions(Auth::user());
            
            return response()->json([
                'success' => true,
                'calendar' => $suggestions['content_calendar'] ?? [],
                'trending_topics' => $suggestions['trending_topics'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Récupération du calendrier échouée'
            ], 500);
        }
    }

    /**
     * Filtre les suggestions selon les critères
     */
    private function filterSuggestions(array $suggestions, array $filters): array
    {
        $filtered = $suggestions;

        if (!empty($filters['topic'])) {
            // Filtrer par sujet
            $topic = strtolower($filters['topic']);
            $filtered['personalized_ideas'] = array_filter(
                $filtered['personalized_ideas'] ?? [],
                fn($idea) => strpos(strtolower(json_encode($idea)), $topic) !== false
            );
        }

        if (!empty($filters['style'])) {
            // Filtrer par style
            $style = $filters['style'];
            $filtered['template_suggestions'] = $filtered['template_suggestions'][$style] ?? [];
        }

        return $filtered;
    }

    /**
     * Obtient une amélioration spécifique
     */
    private function getSpecificImprovement(array $improvements, string $type): array
    {
        switch ($type) {
            case 'grammar':
                return [
                    'type' => 'Correction grammaticale',
                    'content' => $improvements['suggested_content'] ?? '',
                    'changes' => $improvements['improvements'] ?? []
                ];
            
            case 'style':
                return [
                    'type' => 'Amélioration du style',
                    'content' => $improvements['suggested_content'] ?? '',
                    'suggestions' => $improvements['structure_suggestions'] ?? []
                ];
            
            case 'engagement':
                return [
                    'type' => 'Optimisation engagement',
                    'content' => $improvements['suggested_content'] ?? '',
                    'tips' => $improvements['engagement_tips'] ?? [],
                    'call_to_action' => $improvements['call_to_action'] ?? ''
                ];
            
            case 'seo':
                return [
                    'type' => 'Optimisation SEO',
                    'title' => $improvements['title_improvements']['suggested_title'] ?? '',
                    'content' => $improvements['suggested_content'] ?? ''
                ];
            
            default:
                return $improvements;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $publication = Publication::with(['user', 'commentaires.user', 'likes'])
            ->findOrFail($id);
        
        return view('publications.show', compact('publication'));
    }

    /**
     * Serve publication image from storage/public/publications by filename.
     */
    public function image($filename)
    {
        $path = 'publications/' . $filename;
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $stream = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
        $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($path);

        return response($stream, 200)->header('Content-Type', $mime);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $publication = Publication::findOrFail($id);
        
        // Vérifier si l'utilisateur est autorisé à modifier cette publication
        if (Auth::id() !== $publication->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('publications.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
        }
        
        return view('publications.edit', compact('publication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $publication = Publication::findOrFail($id);
        
        // Vérifier si l'utilisateur est autorisé à modifier cette publication
        if (Auth::id() !== $publication->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('publications.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = $request->only(['titre', 'contenu']);
        
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($publication->image) {
                Storage::disk('public')->delete($publication->image);
            }
            
            $imagePath = $request->file('image')->store('publications', 'public');
            $data['image'] = $imagePath;

            // Re-run analysis for updated image
            $service = new ImageEventService();
            $ai = $service->analyze($imagePath, $request->input('contenu'));
            $data['event_type'] = $ai['event_type'];
            $data['ai_description'] = $ai['description'];
            $data['ai_hashtags'] = $ai['hashtags'];
        }
        
        $publication->update($data);
        
        return redirect()->route('publications.show', $publication->id)
            ->with('success', 'Publication mise à jour avec succès!');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $publication = Publication::findOrFail($id);
        
        // Vérifier si l'utilisateur est autorisé à supprimer cette publication
        if (Auth::id() !== $publication->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('publications.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette publication.');
        }
        
        // Supprimer l'image si elle existe
        if ($publication->image) {
            Storage::disk('public')->delete($publication->image);
        }
        
        $publication->delete();
        
        return redirect()->route('publications.index')
            ->with('success', 'Publication supprimée avec succès!');
    }

}
