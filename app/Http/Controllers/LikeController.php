<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Ajouter ou supprimer un like sur une publication
     */
    public function toggleLike($publicationId)
    {
        $publication = Publication::findOrFail($publicationId);
        $user = Auth::user();
        
        $existingLike = Like::where('publication_id', $publication->id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existingLike) {
            // Si un like existe déjà, on le supprime
            $existingLike->delete();
            $message = 'Like retiré';
        } else {
            // Sinon, on crée un nouveau like
            Like::create([
                'publication_id' => $publication->id,
                'user_id' => $user->id
            ]);
            $message = 'Publication likée';
        }
        
        return redirect()->back()->with('success', $message);
    }
}
