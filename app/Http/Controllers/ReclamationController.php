<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
 use Illuminate\Support\Facades\Http; // pour faire les requêtes HTTP
class ReclamationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(): View
{
    // Trier par sentiment : négatif (urgente) → positif → neutre
    $reclamations = Reclamation::with('user')
        ->orderByRaw("
            CASE 
                WHEN sentiment = 'négatif' THEN 1
                WHEN sentiment = 'positif' THEN 2
                WHEN sentiment = 'neutre' OR sentiment IS NULL THEN 3
                ELSE 4
            END
        ")
        ->paginate(10);

    // Ajouter la priorité
    foreach ($reclamations as $rec) {
        $rec->priorite = match ($rec->sentiment) {
            'négatif' => 'urgente',
            'positif', 'neutre', null => 'normale',
            default => 'normale',
        };
    }

    return view('admin.reclamations.index', compact('reclamations'));
}




    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::all();
        return view('admin.reclamations.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sujet' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|in:en_attente,en_cours,traitee',
            'user_id' => 'required|exists:users,id',
        ]);

        Reclamation::create($validated);

        return redirect()->route('admin.reclamations.index')
            ->with('success', 'Réclamation créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $reclamation = Reclamation::with('user', 'avis.user', 'responses.user')->findOrFail($id);
        return view('admin.reclamations.show', compact('reclamation'));
    }

    /**
     * Display the specified public resource with avis.
     */
public function publicShow(string $id): View
{
    $reclamation = Reclamation::with(['user', 'responses.user', 'avis.user'])
        ->findOrFail($id);

    // Déterminer la classe de couleur et le texte de priorité
    $sentiment = strtolower($reclamation->sentiment ?? 'neutre');

    $cardClass = match($sentiment) {
        'positif' => 'card-positive',
        'negatif' => 'card-negative',
        'neutre', null => 'card-neutre',
        default => 'card-neutre',
    };

    $prioriteText = match($sentiment) {
        'positif' => 'Autre',
        'negatif' => 'Urgente',
        'neutre', null => 'Normale',
        default => 'Normale',
    };

    return view('reclamations.show', compact('reclamation', 'cardClass', 'prioriteText'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $reclamation = Reclamation::findOrFail($id);
        $users = User::all();
        return view('admin.reclamations.edit', compact('reclamation', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $reclamation = Reclamation::findOrFail($id);

        $validated = $request->validate([
            'sujet' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|in:en_attente,en_cours,traitee',
            'user_id' => 'required|exists:users,id',
        ]);

        $reclamation->update($validated);

        return redirect()->route('admin.reclamations.index')
            ->with('success', 'Réclamation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $reclamation = Reclamation::findOrFail($id);
        $reclamation->delete();

        return redirect()->route('admin.reclamations.index')
            ->with('success', 'Réclamation supprimée avec succès.');
    }

    /**
     * Display a listing of resolved reclamations for public frontend.
     */
    public function publicIndex(): View
    {
        $search = request('search');
        $status = request('status');
        $query = Reclamation::with('user'); // Show all reclamations publicly

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sujet', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status && $status !== '') {
            $query->where('statut', $status);
        }

        $reclamations = $query->orderBy('created_at', 'desc')->paginate(9)->appends(request()->query());

        // Compute stats for view
        $totalReclamations = Reclamation::count();
        $recentReclamations = Reclamation::latest()->take(5)->count();
        $uniqueUsers = Reclamation::distinct()->count('user_id');
        $resolvedCount = Reclamation::where('statut', 'traitee')->count();

        return view('reclamations.index', compact('reclamations', 'totalReclamations', 'recentReclamations', 'uniqueUsers', 'resolvedCount'));
    }

    /**
     * Store a newly created public reclamation in storage.
     */
   

public function publicStore(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'sujet' => 'required|string|max:255',
        'description' => 'required|string',
    ]);

    $validated['user_id'] = auth()->id();
    $validated['statut'] = 'en_attente';

    // --- Appel API Flask pour le sentiment ---
    try {
        $response = Http::post('http://127.0.0.1:5000/sentiment', [
            'text' => $validated['description']
        ]);

        if ($response->ok()) {
            $sentiment = $response->json()['sentiment'];
            $validated['sentiment'] = $sentiment;

            // Mapper le sentiment en priorité (optionnel)
            $validated['priority'] = $sentiment === 'negatif' ? 'urgente' : 'normale';
        }
    } catch (\Exception $e) {
        // Si l'API ne répond pas, on peut mettre "inconnu"
        $validated['sentiment'] = 'inconnu';
        $validated['priority'] = 'normale';
    }

    Reclamation::create($validated);

    return redirect()->route('reclamations.index')
        ->with('success', 'Votre réclamation a été créée avec succès et est en attente de traitement.');
}

}
