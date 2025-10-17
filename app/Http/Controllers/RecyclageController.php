<?php

namespace App\Http\Controllers;

use App\Models\Recyclage;
use App\Models\TypeRecyclage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecyclageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recyclages = Recyclage::with(['typeRecyclage', 'user'])
            ->latest()
            ->paginate(10);

        return view('recyclages.index', compact('recyclages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $typeRecyclages = TypeRecyclage::actif()->orderBy('nom')->get();
        
        // Vérifier si on a des types de recyclage
        if ($typeRecyclages->isEmpty()) {
            return redirect()->route('recyclages.index')->with('error', 'Aucun type de recyclage disponible. Contactez l\'administrateur.');
        }

        return view('recyclages.create', compact('typeRecyclages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'lieu' => 'required|string|max:255',
            'date_collecte' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'quantite_prevue' => 'nullable|numeric|min:0',
            'type_recyclage_id' => 'required|exists:type_recyclages,id',
            'notes' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['statut'] = 'planifie';

        Recyclage::create($data);

        return redirect()->route('recyclages.index')
            ->with('success', 'Recyclage créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recyclage $recyclage)
    {
        $recyclage->load(['typeRecyclage', 'user']);
        return view('recyclages.show', compact('recyclage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recyclage $recyclage)
    {
        // Vérifier si l'utilisateur peut modifier ce recyclage
        if (Auth::id() !== $recyclage->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('recyclages.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce recyclage.');
        }

        $typeRecyclages = TypeRecyclage::actif()->orderBy('nom')->get();
        return view('recyclages.edit', compact('recyclage', 'typeRecyclages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recyclage $recyclage)
    {
        // Vérifier si l'utilisateur peut modifier ce recyclage
        if (Auth::id() !== $recyclage->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('recyclages.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce recyclage.');
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'lieu' => 'required|string|max:255',
            'date_collecte' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'quantite_prevue' => 'nullable|numeric|min:0',
            'quantite_collectee' => 'nullable|numeric|min:0',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'type_recyclage_id' => 'required|exists:type_recyclages,id',
            'notes' => 'nullable|string'
        ]);

        $recyclage->update($request->all());

        return redirect()->route('recyclages.index')
            ->with('success', 'Recyclage mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recyclage $recyclage)
    {
        // Vérifier si l'utilisateur peut supprimer ce recyclage
        if (Auth::id() !== $recyclage->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('recyclages.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce recyclage.');
        }

        $recyclage->delete();

        return redirect()->route('recyclages.index')
            ->with('success', 'Recyclage supprimé avec succès!');
    }
}
