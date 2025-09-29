<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvisController extends Controller
{
    /**
     * Display a listing of the resource (top-level or for specific reclamation).
     */
    public function index($reclamation = null): View
    {
        if ($reclamation) {
            $reclamation = Reclamation::findOrFail($reclamation);
            $avis = Avis::with('user')->where('reclamation_id', $reclamation->id)->paginate(10);
            return view('admin.reclamations.avis.index', compact('reclamation', 'avis'));
        } else {
            $avis = Avis::with('user', 'reclamation')->paginate(10);
            return view('admin.avis.index', compact('avis'));
        }
    }

    /**
     * Show the form for creating a new resource (top-level or for specific reclamation).
     */
    public function create($reclamation = null): View
    {
        $users = User::all();
        if ($reclamation) {
            $reclamation = Reclamation::findOrFail($reclamation);
            return view('admin.reclamations.avis.create', compact('reclamation', 'users'));
        } else {
            $reclamations = Reclamation::all();
            return view('admin.avis.create', compact('users', 'reclamations'));
        }
    }

    /**
     * Store a newly created resource in storage (top-level or for specific reclamation).
     */
    public function store(Request $request, $reclamation = null): RedirectResponse
    {
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
            'user_id' => 'required|exists:users,id',
            'reclamation_id' => 'required|exists:reclamations,id',
        ]);

        if ($reclamation) {
            $reclamation = Reclamation::findOrFail($reclamation);
            $validated['reclamation_id'] = $reclamation->id;
        }

        Avis::create($validated);

        if ($reclamation) {
            return redirect()->route('admin.reclamations.avis.index', $reclamation)
                ->with('success', 'Avis créé avec succès.');
        } else {
            return redirect()->route('admin.avis.index')
                ->with('success', 'Avis créé avec succès.');
        }
    }

    /**
     * Store a public avis for authenticated user.
     */
    public function publicStore(Request $request, Reclamation $reclamation): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['reclamation_id'] = $reclamation->id;

        $newAvis = Avis::create($validated);
        $newAvis->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'avis' => $newAvis,
                'message' => 'Votre avis a été ajouté avec succès !'
            ]);
        }

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', 'Votre avis a été ajouté avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Avis $avis, Reclamation $reclamation = null): View
    {
        $avis->load('user', 'reclamation');
        if ($reclamation) {
            return view('admin.reclamations.avis.show', compact('reclamation', 'avis'));
        } else {
            $reclamation = $avis->reclamation;
            return view('admin.avis.show', compact('reclamation', 'avis'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avis $avis, Reclamation $reclamation = null): View
    {
        $users = User::all();
        if ($reclamation) {
            return view('admin.reclamations.avis.edit', compact('reclamation', 'avis', 'users'));
        } else {
            return view('admin.avis.edit', compact('avis', 'users'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Avis $avis, Reclamation $reclamation = null): RedirectResponse
    {
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:1000',
            'user_id' => 'required|exists:users,id',
        ]);

        $avis->update($validated);

        if ($reclamation) {
            return redirect()->route('admin.reclamations.avis.index', $reclamation)
                ->with('success', 'Avis mis à jour avec succès.');
        } else {
            return redirect()->route('admin.avis.index')
                ->with('success', 'Avis mis à jour avec succès.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avis $avis, ?Reclamation $reclamation = null): RedirectResponse
    {
        $avis->delete();

        if ($reclamation) {
            return redirect()->route('admin.reclamations.avis.index', $reclamation)
                ->with('success', 'Avis supprimé avec succès.');
        } else {
            return redirect()->route('admin.avis.index')
                ->with('success', 'Avis supprimé avec succès.');
        }
    }

    /**
     * Top-level show.
     */
    public function showTopLevel(Avis $avis): View
    {
        return $this->show($avis, null);
    }

    /**
     * Top-level edit.
     */
    public function editTopLevel(Avis $avis): View
    {
        return $this->edit($avis, null);
    }

    /**
     * Top-level update.
     */
    public function updateTopLevel(Request $request, Avis $avis): RedirectResponse
    {
        return $this->update($request, $avis, null);
    }

    /**
     * Top-level destroy.
     */
    public function destroyTopLevel(Avis $avis): RedirectResponse
    {
        return $this->destroy($avis, null);
    }

}
