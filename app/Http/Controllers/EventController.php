<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Affiche la liste des événements publics
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'active')
                     ->where('start_date', '>=', now())
                     ->with('organizer')
                     ->orderBy('start_date', 'asc');

        // Filtrage par catégorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtrage par ville
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filtrage par type (gratuit/payant)
        if ($request->filled('type')) {
            if ($request->type === 'free') {
                $query->where('is_free', true);
            } elseif ($request->type === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Filtrage par format (en ligne/présentiel)
        if ($request->filled('format')) {
            if ($request->format === 'online') {
                $query->where('is_online', true);
            } elseif ($request->format === 'offline') {
                $query->where('is_online', false);
            }
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%');
            });
        }

        $events = $query->paginate(12);

        // Statistiques pour la page
        $stats = [
            'total' => Event::where('status', 'active')->count(),
            'upcoming' => Event::where('status', 'active')
                              ->where('start_date', '>=', now())
                              ->count(),
            'featured' => Event::where('status', 'active')
                              ->where('is_featured', true)
                              ->count(),
            'categories' => Event::where('status', 'active')
                                ->distinct('category')
                                ->count('category')
        ];

        // Événements mis en avant
        $featuredEvents = Event::where('status', 'active')
                              ->where('is_featured', true)
                              ->where('start_date', '>=', now())
                              ->orderBy('start_date', 'asc')
                              ->limit(3)
                              ->get();

        return view('events.index', compact('events', 'stats', 'featuredEvents'));
    }

    /**
     * Affiche les détails d'un événement
     */
    public function show(Event $event)
    {
        // Vérifier que l'événement est public
        if ($event->status !== 'active') {
            abort(404);
        }

        // Charger les relations nécessaires
        $event->load('organizer');
        
        // Vérifier si l'utilisateur est déjà inscrit
        $isRegistered = false;
        if (auth()->check()) {
            $isRegistered = \App\Models\EventRegistration::where('event_id', $event->id)
                                                        ->where('user_id', auth()->id())
                                                        ->exists();
        }

        // Événements similaires
        $similarEvents = Event::where('status', 'active')
                             ->where('id', '!=', $event->id)
                             ->where('category', $event->category)
                             ->where('start_date', '>=', now())
                             ->orderBy('start_date', 'asc')
                             ->limit(3)
                             ->get();

        return view('events.show', compact('event', 'similarEvents', 'isRegistered'));
    }

    /**
     * Affiche les événements par catégorie
     */
    public function category(Request $request, $category)
    {
        // Vérifier que la catégorie existe
        $categories = Event::getCategories();
        if (!array_key_exists($category, $categories)) {
            abort(404);
        }

        $query = Event::where('status', 'active')
                     ->where('category', $category)
                     ->where('start_date', '>=', now())
                     ->with('organizer')
                     ->orderBy('start_date', 'asc');

        // Appliquer les autres filtres
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('type')) {
            if ($request->type === 'free') {
                $query->where('is_free', true);
            } elseif ($request->type === 'paid') {
                $query->where('is_free', false);
            }
        }

        $events = $query->paginate(12);
        $categoryName = $categories[$category];

        return view('events.category', compact('events', 'category', 'categoryName'));
    }

    /**
     * Recherche d'événements
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        if (empty($search)) {
            return redirect()->route('events.index');
        }

        $events = Event::where('status', 'active')
                      ->where('start_date', '>=', now())
                      ->where(function($query) use ($search) {
                          $query->where('title', 'like', '%' . $search . '%')
                                ->orWhere('description', 'like', '%' . $search . '%')
                                ->orWhere('city', 'like', '%' . $search . '%')
                                ->orWhere('organizer_name', 'like', '%' . $search . '%');
                      })
                      ->with('organizer')
                      ->orderBy('start_date', 'asc')
                      ->paginate(12);

        return view('events.search', compact('events', 'search'));
    }
}
