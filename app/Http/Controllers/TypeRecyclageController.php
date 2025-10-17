<?php

namespace App\Http\Controllers;

use App\Models\TypeRecyclage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeRecyclageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeRecyclages = TypeRecyclage::withCount('recyclages')
            ->orderBy('nom')
            ->paginate(10);

        return view('type-recyclages.index', compact('typeRecyclages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('type-recyclages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:type_recyclages',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7', // Format hex color
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean'
        ]);

        TypeRecyclage::create($request->all());

        return redirect()->route('admin.type-recyclages.index')
            ->with('success', 'Type de recyclage créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeRecyclage $typeRecyclage)
    {
        $typeRecyclage->load(['recyclages' => function($query) {
            $query->with('user')->latest();
        }]);

        return view('type-recyclages.show', compact('typeRecyclage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeRecyclage $typeRecyclage)
    {
        return view('type-recyclages.edit', compact('typeRecyclage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeRecyclage $typeRecyclage)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:type_recyclages,nom,' . $typeRecyclage->id,
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
            'actif' => 'boolean'
        ]);

        $typeRecyclage->update($request->all());

        return redirect()->route('admin.type-recyclages.index')
            ->with('success', 'Type de recyclage mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeRecyclage $typeRecyclage)
    {
        // Vérifier s'il y a des recyclages associés
        if ($typeRecyclage->recyclages()->count() > 0) {
            return redirect()->route('admin.type-recyclages.index')
                ->with('error', 'Impossible de supprimer ce type de recyclage car il est utilisé par des recyclages existants.');
        }

        $typeRecyclage->delete();

        return redirect()->route('admin.type-recyclages.index')
            ->with('success', 'Type de recyclage supprimé avec succès!');
    }
}
