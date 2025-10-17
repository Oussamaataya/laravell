<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with('organizer')
            ->withCount('registrations');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Event::getCategories();
        $statuses = Event::getStatuses();
        $organizers = User::where('role', 'admin')->get();

        return view('admin.events.create', compact('categories', 'statuses', 'organizers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_participants' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|url',
            'category' => 'required|string',
            'eco_impact' => 'nullable|string',
            'carbon_footprint' => 'nullable|numeric|min:0',
            'sustainability_score' => 'nullable|integer|between:0,100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'required|email',
            'organizer_phone' => 'nullable|string|max:20',
            'status' => 'required|in:draft,active,cancelled,completed',
            'is_featured' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_date',
            'requirements' => 'nullable|array',
            'what_to_bring' => 'nullable|array',
            'accessibility_info' => 'nullable|string',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Définir l'utilisateur créateur
        $validated['user_id'] = auth()->id();

        // Convertir les prix gratuits
        if ($validated['is_free']) {
            $validated['price'] = 0;
        }

        $event = Event::create($validated);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Événement créé avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['organizer', 'registrations.user']);
        
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $categories = Event::getCategories();
        $statuses = Event::getStatuses();
        $organizers = User::where('role', 'admin')->get();

        return view('admin.events.edit', compact('event', 'categories', 'statuses', 'organizers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_participants' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|url',
            'category' => 'required|string',
            'eco_impact' => 'nullable|string',
            'carbon_footprint' => 'nullable|numeric|min:0',
            'sustainability_score' => 'nullable|integer|between:0,100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'required|email',
            'organizer_phone' => 'nullable|string|max:20',
            'status' => 'required|in:draft,active,cancelled,completed',
            'is_featured' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_date',
            'requirements' => 'nullable|array',
            'what_to_bring' => 'nullable|array',
            'accessibility_info' => 'nullable|string',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Convertir les prix gratuits
        if ($validated['is_free']) {
            $validated['price'] = 0;
        }

        $event->update($validated);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Événement mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Supprimer l'image associée
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Événement supprimé avec succès !');
    }

    /**
     * Duplicate an event
     */
    public function duplicate(Event $event)
    {
        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copie)';
        $newEvent->status = 'draft';
        $newEvent->current_participants = 0;
        $newEvent->start_date = now()->addWeek();
        $newEvent->end_date = now()->addWeek();
        $newEvent->save();

        return redirect()
            ->route('admin.events.edit', $newEvent)
            ->with('success', 'Événement dupliqué avec succès ! Vous pouvez maintenant le modifier.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);

        $status = $event->is_featured ? 'mis en avant' : 'retiré de la mise en avant';
        
        return back()->with('success', "Événement {$status} avec succès !");
    }
}
