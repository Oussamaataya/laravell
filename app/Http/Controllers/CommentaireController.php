<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CommentToneService;

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

        // Analyze tone and bad-words
        $service = new CommentToneService();
        $analysis = $service->analyze($request->contenu);

        // Reject if it contains bad words or if tone is strongly negative
        if (!empty($analysis['has_bad_words']) || ($analysis['tone'] === 'NEGATIF')) {
            $msg = 'Votre commentaire n\'a pas été publié car il contient un langage inapproprié ou a un ton négatif.';
            if (!empty($analysis['bad_words'])) {
                $msg .= ' Mots détectés: ' . implode(', ', $analysis['bad_words']) . '.';
            }
            return redirect()->back()->with('error', $msg);
        }

        Commentaire::create([
            'contenu' => $request->contenu,
            'publication_id' => $publication->id,
            'user_id' => Auth::id(),
            'tone' => $analysis['tone'],
            'has_bad_words' => $analysis['has_bad_words'],
            'bad_words' => $analysis['bad_words'],
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

        // Re-analyze on update
        $service = new CommentToneService();
        $analysis = $service->analyze($request->contenu);

        if (!empty($analysis['has_bad_words']) || ($analysis['tone'] === 'NEGATIF')) {
            $msg = 'Votre modification n\'a pas été enregistrée car le contenu est inapproprié ou a un ton négatif.';
            if (!empty($analysis['bad_words'])) {
                $msg .= ' Mots détectés: ' . implode(', ', $analysis['bad_words']) . '.';
            }
            return redirect()->back()->with('error', $msg);
        }

        $commentaire->update([
            'contenu' => $request->contenu,
            'tone' => $analysis['tone'],
            'has_bad_words' => $analysis['has_bad_words'],
            'bad_words' => $analysis['bad_words'],
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
