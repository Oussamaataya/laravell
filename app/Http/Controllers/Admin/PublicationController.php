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
        $publications = Publication::with(['user', 'commentaires', 'likes'])->latest()->get();
        return view('admin.publications.index', compact('publications'));
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

        return redirect()->route('admin.publications.index')->with('success', 'Publication supprimée avec succès');
    }

    public function approvePublication($id)
    {
        $publication = Publication::findOrFail($id);
        $publication->is_approved = !$publication->is_approved;
        $publication->save();

        return redirect()->back()->with('success', 'Statut de la publication mis à jour');
    }
}