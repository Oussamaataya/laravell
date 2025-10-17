@extends('layouts.base')

@section('title', 'Événements Écoresponsables')

@section('content')
<div class="container-fluid py-5">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5 mb-5 rounded">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Événements Écoresponsables</h1>
                    <p class="lead mb-4">
                        Découvrez des événements qui allient plaisir et respect de l'environnement. 
                        Participez à des expériences uniques qui contribuent à un avenir durable.
                    </p>
                    <div class="d-flex gap-3">
                        <span class="badge bg-light text-primary fs-6 px-3 py-2">
                            <i class="fas fa-calendar-check me-2"></i>{{ $stats['upcoming'] }} événements à venir
                        </span>
                        <span class="badge bg-light text-primary fs-6 px-3 py-2">
                            <i class="fas fa-star me-2"></i>{{ $stats['featured'] }} événements mis en avant
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="hero-stats">
                        <div class="stat-circle bg-white text-primary d-inline-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 120px; height: 120px;">
                            <div>
                                <h2 class="mb-0 fw-bold">{{ $stats['total'] }}</h2>
                                <small>Événements</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Événements mis en avant -->
        @if($featuredEvents->count() > 0)
            <section class="featured-events mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title">
                        <i class="fas fa-star text-warning me-2"></i>Événements Mis en Avant
                    </h2>
                </div>
                
                <div class="row">
                    @foreach($featuredEvents as $event)
                        <div class="col-lg-4 mb-4">
                            <div class="card event-featured-card h-100 shadow-sm border-0">
                                <div class="position-relative">
                                    @if($event->image)
                                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                             class="card-img-top" style="height: 250px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 250px;">
                                            <i class="fas fa-calendar-alt text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="position-absolute top-0 start-0 p-3">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-star me-1"></i>Mis en avant
                                        </span>
                                    </div>
                                    
                                    @if($event->is_online)
                                        <div class="position-absolute top-0 end-0 p-3">
                                            <span class="badge bg-primary">En ligne</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-info">
                                            {{ App\Models\Event::getCategories()[$event->category] ?? $event->category }}
                                        </span>
                                    </div>
                                    
                                    <h5 class="card-title mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3em;">
                                        <a href="{{ route('events.show', $event) }}" class="text-decoration-none text-dark">
                                            {{ $event->title }}
                                        </a>
                                    </h5>
                                    
                                    @if($event->short_description)
                                        <p class="card-text text-muted mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 4.5em;">
                                            {{ $event->short_description }}
                                        </p>
                                    @else
                                        <p class="card-text text-muted mb-3" style="min-height: 4.5em;"></p>
                                    @endif
                                    
                                    <div class="event-details mb-3 flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <small>{{ $event->start_date->format('d/m/Y') }} à {{ $event->start_time }}</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <small>{{ $event->is_online ? 'En ligne' : $event->city }}</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <small>
                                                {{ $event->current_participants }}
                                                @if($event->max_participants)
                                                    / {{ $event->max_participants }}
                                                @endif
                                                participant(s)
                                            </small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-euro-sign text-primary me-2"></i>
                                            <small class="fw-bold text-success">
                                                @if($event->is_free)
                                                    Gratuit
                                                @else
                                                    {{ number_format($event->price, 2) }}€
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary">
                                            <i class="fas fa-eye me-2"></i>Voir les détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Filtres et recherche -->
        <section class="filters-section mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Titre, description, ville..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Catégorie</label>
                            <select name="category" class="form-select">
                                <option value="">Toutes</option>
                                @foreach(App\Models\Event::getCategories() as $key => $category)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Ville</label>
                            <input type="text" name="city" class="form-control" 
                                   placeholder="Ville..." value="{{ request('city') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">Tous</option>
                                <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Gratuit</option>
                                <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>Payant</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Format</label>
                            <select name="format" class="form-select">
                                <option value="">Tous</option>
                                <option value="online" {{ request('format') == 'online' ? 'selected' : '' }}>En ligne</option>
                                <option value="offline" {{ request('format') == 'offline' ? 'selected' : '' }}>Présentiel</option>
                            </select>
                        </div>
                        
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Liste des événements -->
        <section class="events-list">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt me-2"></i>Tous les Événements
                    <small class="text-muted">({{ $events->total() }} résultats)</small>
                </h2>
                
                <div class="view-toggle">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="grid-view">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="list-view">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="events-container" id="events-grid">
                @forelse($events as $event)
                    <div class="col-lg-4 col-md-6 mb-4 event-item">
                        <div class="card event-card h-100 shadow-sm border-0">
                            <div class="position-relative">
                                @if($event->image)
                                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-calendar-alt text-muted" style="font-size: 2.5rem;"></i>
                                    </div>
                                @endif
                                
                                @if($event->is_online)
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-primary">En ligne</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-info">
                                        {{ App\Models\Event::getCategories()[$event->category] ?? $event->category }}
                                    </span>
                                </div>
                                
                                <h5 class="card-title mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3em;">
                                    <a href="{{ route('events.show', $event) }}" class="text-decoration-none text-dark">
                                        {{ $event->title }}
                                    </a>
                                </h5>
                                
                                @if($event->short_description)
                                    <p class="card-text text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 4.5em;">
                                        {{ $event->short_description }}
                                    </p>
                                @else
                                    <p class="card-text text-muted small mb-3" style="min-height: 4.5em;"></p>
                                @endif
                                
                                <div class="event-details mb-3 flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <small>{{ $event->start_date->format('d/m/Y') }} à {{ $event->start_time }}</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <small>{{ $event->is_online ? 'En ligne' : $event->city }}</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-users text-primary me-2"></i>
                                        <small>
                                            {{ $event->current_participants }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                            participant(s)
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-euro-sign text-primary me-2"></i>
                                        <small class="fw-bold text-success">
                                            @if($event->is_free)
                                                Gratuit
                                            @else
                                                {{ number_format($event->price, 2) }}€
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-2"></i>Voir les détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">Aucun événement trouvé</h4>
                            <p class="text-muted mb-4">
                                Aucun événement ne correspond à vos critères de recherche. 
                                Essayez de modifier vos filtres.
                            </p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>Voir tous les événements
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($events->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $events->withQueryString()->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

<style>
/* ========================================
   UNIFORMISATION DES CARTES D'ÉVÉNEMENTS
   ======================================== */

.event-card,
.event-featured-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Image de taille fixe pour toutes les cartes */
.event-card .card-img-top,
.event-featured-card .card-img-top {
    height: 250px !important;
    width: 100%;
    object-fit: cover;
    object-position: center;
}

/* Placeholder pour les événements sans image */
.event-card .card-img-top.bg-light,
.event-featured-card .card-img-top.bg-light {
    height: 250px !important;
    min-height: 250px !important;
}

/* Corps de la carte avec flex pour uniformiser */
.event-card .card-body,
.event-featured-card .card-body {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: 1.5rem;
}

/* Titre avec hauteur fixe */
.event-card .card-title,
.event-featured-card .card-title {
    min-height: 60px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    margin-bottom: 1rem;
}

/* Description avec hauteur fixe */
.event-card .card-text,
.event-featured-card .card-text {
    min-height: 60px;
    max-height: 60px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    margin-bottom: 1rem;
}

/* Zone des détails avec flex-grow */
.event-card .event-details,
.event-featured-card .event-details {
    flex-grow: 1;
    margin-bottom: 1rem;
}

/* Bouton toujours en bas */
.event-card .d-grid,
.event-featured-card .d-grid {
    margin-top: auto;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.event-featured-card {
    border: 2px solid #ffc107;
}

.event-featured-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
}

.events-container {
    display: flex;
    flex-wrap: wrap;
    margin: -15px;
}

.event-item {
    padding: 15px;
}

.filters-section .card {
    border-radius: 15px;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
}

@media (max-width: 768px) {
    .events-container {
        margin: 0;
    }
    
    .event-item {
        padding: 0 0 30px 0;
    }
    
    .hero-section {
        border-radius: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.querySelectorAll('select[name="category"], select[name="type"], select[name="format"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // View toggle functionality
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const eventsContainer = document.getElementById('events-grid');
    
    if (gridView && listView) {
        gridView.addEventListener('click', function() {
            gridView.classList.add('active');
            listView.classList.remove('active');
            eventsContainer.className = 'events-container row';
        });
        
        listView.addEventListener('click', function() {
            listView.classList.add('active');
            gridView.classList.remove('active');
            eventsContainer.className = 'events-container';
        });
    }
    
    // Animation d'entrée pour les cartes
    const cards = document.querySelectorAll('.event-card, .event-featured-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection
