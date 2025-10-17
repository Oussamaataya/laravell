<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\Campagne;
use Illuminate\Http\Request;

class CollecteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collectes = \App\Models\Collecte::with(['campagne', 'utilisateur'])->paginate(10);
        return view('admin.collectes.index', compact('collectes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campagnes = \App\Models\Campagne::where('statut', 'active')->get();
        $utilisateurs = \App\Models\User::all();
        return view('admin.collectes.create', compact('campagnes', 'utilisateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'methode_paiement' => 'required|in:carte,paypal,virement',
            'statut' => 'required|in:en_attente,validé,échoué',
            'campagne_id' => 'required|exists:compagnes,id',
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        \App\Models\Collecte::create($validated);

        return redirect()->route('admin.collectes.index')->with('success', 'Collecte créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Collecte $collecte)
    {
        $collecte->load(['campagne', 'utilisateur']);
        return view('admin.collectes.show', compact('collecte'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collecte $collecte)
    {
        $collecte->load(['campagne', 'utilisateur']);
        $campagnes = \App\Models\Campagne::where('statut', 'active')->get();
        $utilisateurs = \App\Models\User::all();
        return view('admin.collectes.edit', compact('collecte', 'campagnes', 'utilisateurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collecte $collecte)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'methode_paiement' => 'required|in:carte,paypal,virement',
            'statut' => 'required|in:en_attente,validé,échoué',
            'campagne_id' => 'required|exists:compagnes,id',
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        $collecte->update($validated);

        return redirect()->route('admin.collectes.index')->with('success', 'Collecte modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collecte $collecte)
    {
        $collecte->delete();

        return redirect()->route('admin.collectes.index')->with('success', 'Collecte supprimée avec succès.');
    }

    /**
     * Show the donation form for a campaign.
     */
    public function donateForm(Campagne $campagne)
    {
        // Load related data for the form
        $campagne->load(['collectes' => function ($query) {
            $query->latest()->limit(5);
        }]);

        $totalCollected = $campagne->collectes->sum('montant');
        $progress = $campagne->montant_objectif > 0 ? ($totalCollected / $campagne->montant_objectif) * 100 : 0;
        $numberOfDonors = $campagne->collectes->count();

        return view('collectes.donate', compact('campagne', 'totalCollected', 'progress', 'numberOfDonors'));
    }

    /**
     * Handle public donation for a campaign.
     */
    public function donate(Request $request, Campagne $campagne)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0.01|max:' . $campagne->montant_objectif,
            'message' => 'nullable|string|max:255',
        ]);

        $validated = $request->only(['montant', 'message']);
        $validated['methode_paiement'] = 'carte'; // Default payment method
        $validated['statut'] = 'en_attente'; // Default status
        $validated['campagne_id'] = $campagne->id;
        $validated['utilisateur_id'] = auth()->id();

        // Create the donation
        $collecte = Collecte::create($validated);

        // Update campaign current amount
        $campagne->increment('montant_actuel', $request->montant);

        return redirect()->route('collectes.donate.form', $campagne)
                         ->with('success', 'Votre don de ' . number_format($request->montant, 2) . ' € a été enregistré avec succès ! Merci pour votre soutien.');
    }
}
