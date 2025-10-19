<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Commentaire;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index()
    {
        $query = Publication::with(['user', 'commentaires', 'likes']);

        // Filters: status (approved/pending), author, search text, date range
        if (request()->filled('status')) {
            if (request('status') === 'approved') {
                $query->where('is_approved', true);
            } elseif (request('status') === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if (request()->filled('author')) {
            $query->where('user_id', request('author'));
        }

        if (request()->filled('q')) {
            $q = request('q');
            $query->where(function($qry) use ($q) {
                $qry->where('titre', 'like', "%{$q}%")
                    ->orWhere('contenu', 'like', "%{$q}%");
            });
        }

        if (request()->filled('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $publications = $query->latest()->paginate(15)->withQueryString();

        // Authors for filter
        $authors = \App\Models\User::orderBy('name')->pluck('name', 'id');

        return view('admin.publications.index', compact('publications', 'authors'));
    }

    public function create()
    {
        return view('admin.publications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $publication = new Publication();
        $publication->titre = $request->titre;
        $publication->contenu = $request->contenu;
        $publication->user_id = auth()->id();
        $publication->is_approved = true;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('publications', 'public');
            // store() returns path relative to the disk root (e.g. 'publications/xxx.jpg')
            $publication->image = $path;
        }

        $publication->save();

        return redirect()->route('admin.publications.index')->with('success', 'Publication créée avec succès');
    }

    public function edit($id)
    {
        $publication = Publication::findOrFail($id);
        return view('admin.publications.edit', compact('publication'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $publication = Publication::findOrFail($id);
        $publication->titre = $request->titre;
        $publication->contenu = $request->contenu;

        if ($request->hasFile('image')) {
            if ($publication->image) {
                // delete previous image from the public disk
                Storage::disk('public')->delete($publication->image);
            }
            $path = $request->file('image')->store('publications', 'public');
            $publication->image = $path;
        }

        $publication->save();

        return redirect()->route('admin.publications.index')->with('success', 'Publication mise à jour avec succès');
    }

    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);
        
        // Supprimer les likes associés
        Like::where('publication_id', $id)->delete();
        
        // Supprimer les commentaires associés
        Commentaire::where('publication_id', $id)->delete();
        
        // Supprimer l'image si elle existe
        if ($publication->image) {
            Storage::disk('public')->delete($publication->image);
        }
        
        $publication->delete();

        // Retourner à la page précédente pour conserver les filtres et la pagination
        return redirect()->back()->with('success', 'Publication supprimée avec succès');
    }

    /**
     * Bulk actions for publications (approve/delete)
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Aucune publication sélectionnée');
        }

        if ($action === 'delete') {
            foreach ($ids as $id) {
                $pub = Publication::find($id);
                if ($pub) {
                    // delete related
                    \App\Models\Like::where('publication_id', $id)->delete();
                    \App\Models\Commentaire::where('publication_id', $id)->delete();
                    if ($pub->image) {
                        Storage::disk('public')->delete($pub->image);
                    }
                    $pub->delete();
                }
            }
            return redirect()->back()->with('success', 'Publications supprimées');
        }

        if ($action === 'approve') {
            Publication::whereIn('id', $ids)->update(['is_approved' => true]);
            return redirect()->back()->with('success', 'Publications approuvées');
        }

        return redirect()->back()->with('error', 'Action inconnue');
    }

    public function approvePublication($id)
    {
        $publication = Publication::findOrFail($id);
        $publication->is_approved = !$publication->is_approved;
        $publication->save();

        return redirect()->back()->with('success', 'Statut de la publication mis à jour');
    }

    /**
     * Display the specified publication (admin view)
     */
    public function show($id)
    {
        $publication = Publication::with(['user', 'commentaires', 'likes'])->findOrFail($id);
        return view('admin.publications.show', compact('publication'));
    }
}