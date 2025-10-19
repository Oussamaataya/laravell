<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commentaire;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    public function index()
    {
        $query = Commentaire::with(['user', 'publication']);

        if (request()->filled('author')) {
            $query->where('user_id', request('author'));
        }

        if (request()->filled('publication')) {
            $query->where('publication_id', request('publication'));
        }

        if (request()->filled('q')) {
            $q = request('q');
            $query->where('contenu', 'like', "%{$q}%");
        }

        if (request()->filled('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $commentaires = $query->latest()->paginate(15)->withQueryString();

        $authors = \App\Models\User::orderBy('name')->pluck('name', 'id');
        $publications = \App\Models\Publication::orderBy('titre')->pluck('titre', 'id');

        return view('admin.commentaires.index', compact('commentaires', 'authors', 'publications'));
    }

    public function destroy($id)
    {
        $commentaire = Commentaire::findOrFail($id);
        $commentaire->delete();
        return redirect()->back()->with('success', 'Commentaire supprimé avec succès');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Aucun commentaire sélectionné');
        }

        if ($action === 'delete') {
            Commentaire::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Commentaires supprimés');
        }

        return redirect()->back()->with('error', 'Action inconnue');
    }
}
