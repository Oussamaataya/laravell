<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentaireController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Publication $publication)
    {
        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        Commentaire::create([
            'contenu' => $request->contenu,
            'publication_id' => $publication->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Commentaire ajouté avec succès!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $commentaire = Commentaire::findOrFail($id);

        // Vérifier si l'utilisateur est autorisé à modifier ce commentaire
        if (Auth::id() !== $commentaire->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        $commentaire->update([
            'contenu' => $request->contenu,
        ]);

        return redirect()->back()->with('success', 'Commentaire mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $commentaire = Commentaire::findOrFail($id);

        // Vérifier si l'utilisateur est autorisé à supprimer ce commentaire
        if (Auth::id() !== $commentaire->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        $commentaire->delete();

        return redirect()->back()->with('success', 'Commentaire supprimé avec succès!');
    }
}
