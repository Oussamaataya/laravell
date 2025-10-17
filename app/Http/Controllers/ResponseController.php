<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResponseController extends Controller
{
    /**
     * Store a newly created response in storage.
     */
    public function store(Request $request, string $reclamationId): RedirectResponse
    {
        $reclamation = Reclamation::findOrFail($reclamationId);

        $validated = $request->validate([
            'contenu' => 'required|string',
            'statut' => 'nullable|in:en_attente,en_cours,traitee',
        ]);

        Response::create([
            'reclamation_id' => $reclamation->id,
            'user_id' => auth()->id(),
            'contenu' => $validated['contenu'],
        ]);

        // Mettre à jour le statut de la réclamation si fourni
        if (isset($validated['statut'])) {
            $reclamation->update(['statut' => $validated['statut']]);
        }

        return redirect()->route('admin.reclamations.show', $reclamation)
            ->with('success', 'Réponse ajoutée avec succès.');
    }

    /**
     * Update the specified response in storage.
     */
    public function update(Request $request, string $reclamationId, string $id): RedirectResponse
    {
        $response = Response::findOrFail($id);
        $reclamation = Reclamation::findOrFail($reclamationId);

        $validated = $request->validate([
            'contenu' => 'required|string',
        ]);

        $response->update($validated);

        return redirect()->route('admin.reclamations.show', $reclamation)
            ->with('success', 'Réponse mise à jour avec succès.');
    }

    /**
     * Remove the specified response from storage.
     */
    public function destroy(string $reclamationId, string $id): RedirectResponse
    {
        $response = Response::findOrFail($id);
        $reclamation = Reclamation::findOrFail($reclamationId);
        
        $response->delete();

        return redirect()->route('admin.reclamations.show', $reclamation)
            ->with('success', 'Réponse supprimée avec succès.');
    }
}
