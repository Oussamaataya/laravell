<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publications = Publication::where('is_approved', true)
            ->with(['user', 'commentaires', 'likes'])
            ->latest()
            ->paginate(10);
        
        return view('publications.index', compact('publications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('publications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['titre', 'contenu']);
        $data['user_id'] = Auth::id();
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('publications', 'public');
            $data['image'] = $imagePath;
        }

        Publication::create($data);

        return redirect()->route('publications.index')
            ->with('success', 'Publication créée avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $publication = Publication::with(['user', 'commentaires.user', 'likes'])
            ->findOrFail($id);
        
        return view('publications.show', compact('publication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $publication = Publication::findOrFail($id);
        
        // Vérifier si l'utilisateur est autorisé à modifier cette publication
        if (Auth::id() !== $publication->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('publications.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
        }
        
        return view('publications.edit', compact('publication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $publication = Publication::findOrFail($id);
        
        // Vérifier si l'utilisateur est autorisé à modifier cette publication
        if (Auth::id() !== $publication->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('publications.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = $request->only(['titre', 'contenu']);
        
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($publication->image) {
                Storage::disk('public')->delete($publication->image);
            }
            
            $imagePath = $request->file('image')->store('publications', 'public');
            $data['image'] = $imagePath;
        }
        
        $publication->update($data);
        
        return redirect()->route('publications.show', $publication->id)
            ->with('success', 'Publication mise à jour avec succès!');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $publication = Publication::findOrFail($id);
        
        // Vérifier si l'utilisateur est autorisé à supprimer cette publication
        if (Auth::id() !== $publication->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->route('publications.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette publication.');
        }
        
        // Supprimer l'image si elle existe
        if ($publication->image) {
            Storage::disk('public')->delete($publication->image);
        }
        
        $publication->delete();
        
        return redirect()->route('publications.index')
            ->with('success', 'Publication supprimée avec succès!');
    }

}
