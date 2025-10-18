@extends('layouts.back')

@section('title', 'Gestion des Événements')

@section('content')
<div class="main-panel event-page">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card event-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Gestion des Événements</h4>
                        <p class="text-muted mb-0">Gérez tous vos événements éco-responsables</p>
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-lg">
                            <i class="mdi mdi-plus-circle mr-2"></i> Créer un Événement
                        </a>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.events.index') }}" class="row event-form">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-control">
                                    <option value="">Toutes catégories</option>
                                    @foreach(App\Models\Event::getCategories() as $key => $category)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">Tous statuts</option>
                                    @foreach(App\Models\Event::getStatuses() as $key => $status)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}" placeholder="Date début">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}" placeholder="Date fin">
                            </div>
                            <div class="col-md-1">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary" title="Filtrer">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary" title="Reset">
                                        <i class="mdi mdi-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mb-4 event-stats">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1 text-white-50">Total Événements</h6>
                                        <h2 class="mb-0 fw-bold">{{ $events->total() }}</h2>
                                    </div>
                                    <div class="icon-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-calendar-multiple mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-success text-white border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1 text-white-50">Événements Actifs</h6>
                                        <h2 class="mb-0 fw-bold">{{ $events->where('status', 'active')->count() }}</h2>
                                    </div>
                                    <div class="icon-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-calendar-check mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-warning text-white border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1 text-white-50">Brouillons</h6>
                                        <h2 class="mb-0 fw-bold">{{ $events->where('status', 'draft')->count() }}</h2>
                                    </div>
                                    <div class="icon-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-file-document-edit mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-info text-white border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1 text-white-50">Mis en Avant</h6>
                                        <h2 class="mb-0 fw-bold">{{ $events->where('is_featured', true)->count() }}</h2>
                                    </div>
                                    <div class="icon-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-star mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cartes des événements -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    @forelse($events as $event)
                        <div class="col">
                            <div class="card event-card h-100 shadow-sm border-0" style="transition: all 0.3s ease; display: flex; flex-direction: column;">
                                <!-- Image de l'événement -->
                                <div class="position-relative">
                                    @if($event->image)
                                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                             class="card-img-top" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="mdi mdi-calendar-outline text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Badges en overlay -->
                                    <div class="position-absolute" style="top: 10px; left: 10px;">
                                        @if($event->is_featured)
                                            <span class="badge badge-warning">
                                                <i class="mdi mdi-star"></i> Mis en avant
                                            </span>
                                        @endif
                                        @if($event->is_online)
                                            <span class="badge badge-primary ml-1">En ligne</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Statut -->
                                    <div class="position-absolute" style="top: 10px; right: 10px;">
                                        @switch($event->status)
                                            @case('active')
                                                <span class="badge badge-success">Actif</span>
                                                @break
                                            @case('draft')
                                                <span class="badge badge-warning">Brouillon</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-danger">Annulé</span>
                                                @break
                                            @case('completed')
                                                <span class="badge badge-secondary">Terminé</span>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column" style="flex: 1;">
                                    <!-- Catégorie -->
                                    <div class="mb-2">
                                        <span class="badge badge-info">
                                            {{ App\Models\Event::getCategories()[$event->category] ?? $event->category }}
                                        </span>
                                    </div>
                                    
                                    <!-- Titre -->
                                    <h5 class="card-title mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3em;">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-decoration-none text-dark">
                                            {{ $event->title }}
                                        </a>
                                    </h5>
                                    
                                    <!-- Description courte -->
                                    @if($event->short_description)
                                        <p class="card-text text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 4.5em;">
                                            {{ $event->short_description }}
                                        </p>
                                    @else
                                        <p class="card-text text-muted small mb-3" style="min-height: 4.5em;"></p>
                                    @endif
                                    
                                    <!-- Informations clés -->
                                    <div class="mb-3 flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="mdi mdi-calendar text-primary mr-2"></i>
                                            <small>{{ $event->start_date->format('d/m/Y') }} à {{ $event->start_time }}</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="mdi mdi-map-marker text-primary mr-2"></i>
                                            <small>{{ $event->is_online ? 'En ligne' : $event->city }}</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="mdi mdi-account-group text-primary mr-2"></i>
                                            <small>
                                                {{ $event->current_participants }}
                                                @if($event->max_participants)
                                                    / {{ $event->max_participants }}
                                                @endif
                                                participant(s)
                                            </small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-cash text-primary mr-2"></i>
                                            <small class="font-weight-bold text-success">{{ $event->formatted_price }}</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Score écologique -->
                                    <div class="mb-3" style="min-height: 45px;">
                                        @if($event->sustainability_score)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Impact écologique</small>
                                                <span class="badge badge-{{ $event->eco_impact_badge['class'] }} small">
                                                    {{ $event->eco_impact_badge['label'] }}
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar progress-bar-{{ $event->eco_impact_badge['class'] }}" 
                                                     style="width: {{ $event->sustainability_score }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="card-footer bg-transparent border-0 pt-0 mt-auto">
                                    <div class="d-flex">
                                        <a href="{{ route('admin.events.show', $event) }}" 
                                           class="btn btn-outline-primary btn-sm mr-2 flex-fill">
                                            <i class="mdi mdi-eye"></i> Voir
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event) }}" 
                                           class="btn btn-primary btn-sm mr-2 flex-fill">
                                            <i class="mdi mdi-pencil"></i> Modifier
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('admin.events.duplicate', $event) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="mdi mdi-content-copy"></i> Dupliquer
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.events.toggle-featured', $event) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="mdi mdi-star{{ $event->is_featured ? '-off' : '' }}"></i>
                                                            {{ $event->is_featured ? 'Retirer' : 'Mettre' }} en avant
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" 
                                                          class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="mdi mdi-delete"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="table-responsive">
                            <table class="table table-hover align-middle events-table">
                                <div class="mb-4">
                                    <i class="mdi mdi-calendar-remove text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-muted mb-3">Aucun événement trouvé</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier événement éco-responsable</p>
                                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-lg">
                                    <i class="mdi mdi-plus-circle me-2"></i> Créer le premier événement
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($events->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ========================================
       UNIFORMISATION DES CARTES D'ÉVÉNEMENTS ADMIN
       ======================================== */
    
    /* Forcer la grille à avoir des colonnes égales */
    .row.row-cols-1.row-cols-md-2.row-cols-xl-3 {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 1.5rem;
        margin: 0 -0.75rem;
    }
    
    .row.row-cols-1.row-cols-md-2.row-cols-xl-3 > .col {
        flex: 1 1 calc(33.333% - 1.5rem);
        max-width: calc(33.333% - 1.5rem);
        padding: 0;
    }
    
    @media (max-width: 1200px) {
        .row.row-cols-1.row-cols-md-2.row-cols-xl-3 > .col {
            flex: 1 1 calc(50% - 1.5rem);
            max-width: calc(50% - 1.5rem);
        }
    }
    
    @media (max-width: 768px) {
        .row.row-cols-1.row-cols-md-2.row-cols-xl-3 > .col {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
    
    /* Structure de la carte - HAUTEUR TOTALE FIXE */
    .event-card {
        height: 100% !important;
        min-height: 680px !important;
        max-height: 680px !important;
        display: flex !important;
        flex-direction: column !important;
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e0e0e0;
    }
    
    /* IMAGE - HAUTEUR FIXE STRICTE */
    .event-card .position-relative {
        height: 200px !important;
        min-height: 200px !important;
        max-height: 200px !important;
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .event-card .card-img-top {
        height: 200px !important;
        min-height: 200px !important;
        max-height: 200px !important;
        width: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    /* Placeholder pour les événements sans image */
    .event-card .card-img-top.bg-light {
        height: 200px !important;
        min-height: 200px !important;
        max-height: 200px !important;
    }
    
    /* CORPS DE LA CARTE - Hauteur flexible mais contrôlée */
    .event-card .card-body {
        flex: 1 1 auto !important;
        display: flex !important;
        flex-direction: column !important;
        padding: 1.25rem !important;
        overflow: hidden;
        max-height: calc(680px - 200px - 70px) !important; /* Total - Image - Footer */
    }
    
    /* Badge catégorie */
    .event-card .card-body > .mb-2:first-child {
        height: 28px !important;
        margin-bottom: 0.5rem !important;
        flex-shrink: 0;
    }
    
    /* TITRE - HAUTEUR FIXE */
    .event-card .card-title {
        height: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
        line-height: 1.25 !important;
        margin-bottom: 0.75rem !important;
        font-size: 1.1rem !important;
        flex-shrink: 0;
    }
    
    /* DESCRIPTION - HAUTEUR FIXE */
    .event-card .card-text {
        height: 63px !important;
        min-height: 63px !important;
        max-height: 63px !important;
        overflow: hidden !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 3 !important;
        -webkit-box-orient: vertical !important;
        margin-bottom: 1rem !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        flex-shrink: 0;
    }
    
    /* INFORMATIONS CLÉS - HAUTEUR FIXE */
    .event-card .card-body > .mb-3:nth-child(4) {
        height: 110px !important;
        min-height: 110px !important;
        max-height: 110px !important;
        flex-shrink: 0;
        margin-bottom: 0.75rem !important;
    }
    
    /* SCORE ÉCOLOGIQUE - HAUTEUR FIXE */
    .event-card .card-body > .mb-3:last-child {
        height: 55px !important;
        min-height: 55px !important;
        max-height: 55px !important;
        flex-shrink: 0;
        margin-bottom: 0 !important;
    }
    
    /* FOOTER - HAUTEUR FIXE */
    .event-card .card-footer {
        height: 70px !important;
        min-height: 70px !important;
        max-height: 70px !important;
        margin-top: auto !important;
        padding: 1rem 1.25rem !important;
        background-color: #f8f9fa !important;
        border-top: 1px solid #dee2e6 !important;
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }
    
    /* Effets hover */
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
        border-color: #6c7ae0;
    }
    
    .event-card .card-title a {
        color: #2c2c2c;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .event-card .card-title a:hover {
        color: #6c7ae0 !important;
    }
    
    /* Badges et icônes */
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        font-weight: 500;
    }
    
    .icon-lg {
        width: 50px;
        height: 50px;
    }
    
    /* Gradients pour les stats */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    }
    
    /* Boutons */
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    /* Dropdown */
    .dropdown-menu {
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid #e0e0e0;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    /* Progress bars */
    .progress {
        height: 6px !important;
        border-radius: 3px;
        background-color: #e9ecef;
    }
    
    .progress-bar-success {
        background-color: #28a745 !important;
    }
    
    .progress-bar-primary {
        background-color: #007bff !important;
    }
    
    .progress-bar-warning {
        background-color: #ffc107 !important;
    }
    
    .progress-bar-secondary {
        background-color: #6c757d !important;
    }
    
    /* Icônes dans les informations */
    .event-card .d-flex.align-items-center .mdi {
        font-size: 1rem;
        width: 20px;
    }
    
    /* Fix pour les badges en overlay */
    .position-absolute .badge {
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.querySelectorAll('select[name="category"], select[name="status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Animation d'entrée pour les cartes
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.event-card');
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
@endpush

@endsection
