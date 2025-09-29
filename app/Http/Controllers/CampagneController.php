<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\User;
use Illuminate\Http\Request;

class CampagneController extends Controller
{
    /**
     * Display a listing of active campagnes for frontend.
     */
    public function publicIndex()
    {
        $search = request('search');
        $status = request('status');

        $query = Campagne::with('organisateur', 'collectes')
                         ->withCount('collectes')
                         ->withSum('collectes', 'montant')
                         ->orderBy('date_debut', 'desc');

        if ($search) {
            $query->where('nom', 'like', '%' . $search . '%');
        }

        if ($status) {
            $query->where('statut', $status);
        }

        $campagnes = $query->paginate(9)->appends(request()->query()); // Preserve query params in pagination

        return view('collectes.index', compact('campagnes'));
    }

    /**
     * Display the specified campagne for frontend.
     */
    public function publicShow(Campagne $campagne)
    {
        if ($campagne->statut !== 'active') {
            abort(404);
        }

        $campagne->load('organisateur', 'collectes');
        $totalCollected = $campagne->collectes->sum('montant');
        $progress = $campagne->montant_objectif > 0 ? ($totalCollected / $campagne->montant_objectif) * 100 : 0;
        $numberOfDonors = $campagne->collectes->count();
        $recentCollectes = $campagne->collectes->sortByDesc('created_at')->take(5);

        return view('collectes.show', compact('campagne', 'totalCollected', 'progress', 'numberOfDonors', 'recentCollectes'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campagnes = Campagne::with('organisateur', 'collectes')->withCount('collectes')->paginate(10);
        return view('admin.campagnes.index', compact('campagnes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $utilisateurs = User::all();
        return view('admin.campagnes.create', compact('utilisateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant_objectif' => 'required|numeric|min:0.01',
            'montant_actuel' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'statut' => 'required|in:brouillon,active,terminée,annulée',
            'organisateur_id' => 'required|exists:users,id',
        ]);

        Campagne::create($validated);

        return redirect()->route('admin.campagnes.index')->with('success', 'Campagne créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campagne $campagne)
    {
        $campagne->load('organisateur', 'collectes');
        return view('admin.campagnes.show', compact('campagne'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campagne $campagne)
    {
        $campagne->load('organisateur');
        $utilisateurs = User::all();
        return view('admin.campagnes.edit', compact('campagne', 'utilisateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campagne $campagne)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant_objectif' => 'required|numeric|min:0.01',
            'montant_actuel' => 'nullable|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'statut' => 'required|in:brouillon,active,terminée,annulée',
            'organisateur_id' => 'required|exists:users,id',
        ]);

        $campagne->update($validated);

        return redirect()->route('admin.campagnes.index')->with('success', 'Campagne modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campagne $campagne)
    {
        $campagne->delete();

        return redirect()->route('admin.campagnes.index')->with('success', 'Campagne supprimée avec succès.');
    }
}
