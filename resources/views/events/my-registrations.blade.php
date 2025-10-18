@extends('layouts.base')

@section('title', 'Mes Inscriptions')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-primary mb-3">
                <i class="fas fa-calendar-check me-3"></i>Mes Inscriptions
            </h1>
            <p class="lead text-muted">Gérez vos inscriptions aux événements écoresponsables</p>
        </div>
    </div>

    @if($registrations->count() > 0)
        <!-- Statistiques -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card bg-primary text-white border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x mb-3"></i>
                        <h3 class="mb-1">{{ $registrations->count() }}</h3>
                        <p class="mb-0">Inscriptions totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-3"></i>
                        <h3 class="mb-1">{{ $registrations->filter(function($reg) { return $reg->event->start_date >= now()->toDateString(); })->count() }}</h3>
                        <p class="mb-0">À venir</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-3"></i>
                        <h3 class="mb-1">{{ $registrations->filter(function($reg) { return $reg->event->start_date < now()->toDateString(); })->count() }}</h3>
                        <p class="mb-0">Terminés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des inscriptions -->
        <div class="row">
            @foreach($registrations->sortBy(function($registration) { return $registration->event->start_date; }) as $registration)
                @php
                    $event = $registration->event;
                    $isPast = $event->start_date < now()->toDateString();
                    $isToday = $event->start_date->isToday();
                @endphp
                
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 {{ $isPast ? 'opacity-75' : '' }}">
                        <div class="position-relative">
                            @if($event->image)
                                <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                     class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-calendar-alt text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Status badges -->
                            <div class="position-absolute top-0 start-0 p-3">
                                @if($isPast)
                                    <span class="badge bg-secondary">Terminé</span>
                                @elseif($isToday)
                                    <span class="badge bg-warning text-dark">Aujourd'hui</span>
                                @else
                                    <span class="badge bg-success">À venir</span>
                                @endif
                            </div>
                            
                            @if($event->is_online)
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-primary">En ligne</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <!-- Catégorie -->
                            <div class="mb-2">
                                <span class="badge bg-info">
                                    {{ App\Models\Event::getCategories()[$event->category] ?? $event->category }}
                                </span>
                            </div>
                            
                            <!-- Titre -->
                            <h5 class="card-title mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 3em;">
                                <a href="{{ route('events.show', $event) }}" class="text-decoration-none text-dark">
                                    {{ $event->title }}
                                </a>
                            </h5>
                            
                            <!-- Informations clés -->
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
                                    <i class="fas fa-calendar-plus text-success me-2"></i>
                                    <small>Inscrit le {{ ($registration->registered_at ?? $registration->created_at)->format('d/m/Y') }}</small>
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
                            
                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>Voir
                                </a>
                                
                                <a href="{{ route('events.ticket', $registration->id) }}" class="btn btn-primary flex-fill">
                                    <i class="fas fa-ticket-alt me-1"></i>Billet
                                </a>
                                
                                @if(!$isPast && !$isToday)
                                    @php
                                        $eventDateTime = \Carbon\Carbon::parse($event->start_date->format('Y-m-d') . ' ' . $event->start_time);
                                        $canUnregister = $eventDateTime->diffInHours(now()) >= 24;
                                    @endphp
                                    
                                    @if($canUnregister)
                                        <form method="POST" action="{{ route('events.unregister', $event) }}" class="flex-fill">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir vous désinscrire ?')">
                                                <i class="fas fa-user-minus me-1"></i>Désinscrire
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-outline-secondary flex-fill" disabled title="Désinscription impossible moins de 24h avant l'événement">
                                            <i class="fas fa-lock me-1"></i>Verrouillé
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Aucune inscription -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-muted mb-3">Aucune inscription</h4>
            <p class="text-muted mb-4">
                Vous n'êtes inscrit à aucun événement pour le moment. 
                Découvrez nos événements écoresponsables et participez à des expériences uniques !
            </p>
            <a href="{{ route('events.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-calendar-alt me-2"></i>Découvrir les événements
            </a>
        </div>
    @endif
</div>

<style>
/* ========================================
   UNIFORMISATION DES CARTES - MES INSCRIPTIONS
   ======================================== */

.card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Image de taille fixe */
.card .card-img-top {
    height: 200px !important;
    width: 100%;
    object-fit: cover;
    object-position: center;
}

/* Placeholder pour les événements sans image */
.card .card-img-top.bg-light {
    height: 200px !important;
    min-height: 200px !important;
}

/* Corps de la carte avec flex */
.card .card-body {
    display: flex;
    flex-direction: column;
    flex: 1;
}

/* Titre avec hauteur minimale */
.card .card-title {
    min-height: 50px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

/* Description avec hauteur fixe */
.card .card-text {
    min-height: 60px;
    max-height: 60px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

/* Zone des actions toujours en bas */
.card .d-flex.gap-2 {
    margin-top: auto;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.opacity-75 {
    opacity: 0.75;
}

@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée pour les cartes
    const cards = document.querySelectorAll('.card');
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
