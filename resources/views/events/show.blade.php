@extends('layouts.base')

@section('title', $event->title)

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
            <li class="breadcrumb-item active">{{ $event->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- Image principale -->
            <div class="event-image mb-4">
                @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                         class="img-fluid rounded shadow" style="width: 100%; height: 400px; object-fit: cover;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" 
                         style="width: 100%; height: 400px;">
                        <i class="fas fa-calendar-alt text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
            </div>

            <!-- Titre et badges -->
            <div class="event-header mb-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-info fs-6 px-3 py-2">
                        {{ App\Models\Event::getCategories()[$event->category] ?? $event->category }}
                    </span>
                    @if($event->is_featured)
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="fas fa-star me-1"></i>Mis en avant
                        </span>
                    @endif
                    @if($event->is_online)
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-video me-1"></i>En ligne
                        </span>
                    @endif
                    @if($event->is_free)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-gift me-1"></i>Gratuit
                        </span>
                    @endif
                </div>
                
                <h1 class="display-5 fw-bold text-primary mb-3">{{ $event->title }}</h1>
                
                @if($event->short_description)
                    <p class="lead text-muted">{{ $event->short_description }}</p>
                @endif
            </div>

            <!-- Informations clés -->
            <div class="event-details card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informations Pratiques
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar text-primary me-3" style="width: 20px;"></i>
                                <div>
                                    <strong>Date</strong><br>
                                    <span>{{ $event->start_date->format('d/m/Y') }}</span>
                                    @if(!$event->start_date->isSameDay($event->end_date))
                                        <span> au {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-primary me-3" style="width: 20px;"></i>
                                <div>
                                    <strong>Horaires</strong><br>
                                    <span>{{ $event->start_time }} - {{ $event->end_time }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-3" style="width: 20px;"></i>
                                <div>
                                    <strong>Lieu</strong><br>
                                    @if($event->is_online)
                                        <span>Événement en ligne</span>
                                    @else
                                        <span>{{ $event->location ?: $event->city }}</span>
                                        @if($event->address)
                                            <br><small class="text-muted">{{ $event->address }}</small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-primary me-3" style="width: 20px;"></i>
                                <div>
                                    <strong>Participants</strong><br>
                                    <span>{{ $event->current_participants }}</span>
                                    @if($event->max_participants)
                                        <span> / {{ $event->max_participants }} places</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-euro-sign text-primary me-3" style="width: 20px;"></i>
                                <div>
                                    <strong>Tarif</strong><br>
                                    @if($event->is_free)
                                        <span class="text-success fw-bold">Gratuit</span>
                                    @else
                                        <span class="fw-bold">{{ number_format($event->price, 2) }}€</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-primary me-3" style="width: 20px;"></i>
                                <div>
                                    <strong>Organisateur</strong><br>
                                    <span>{{ $event->organizer_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description complète -->
            <div class="event-description card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">
                        <i class="fas fa-align-left text-primary me-2"></i>Description
                    </h3>
                    <div class="content">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Impact écologique -->
            @if($event->eco_impact || $event->sustainability_score)
                <div class="eco-impact card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-leaf text-success me-2"></i>Impact Écologique
                        </h3>
                        
                        @if($event->sustainability_score)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Score de durabilité</span>
                                    <span class="fw-bold">{{ $event->sustainability_score }}/100</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: {{ $event->sustainability_score }}%"></div>
                                </div>
                            </div>
                        @endif
                        
                        @if($event->eco_impact)
                            <div class="eco-description">
                                {!! nl2br(e($event->eco_impact)) !!}
                            </div>
                        @endif
                        
                        @if($event->carbon_footprint)
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-cloud me-1"></i>
                                    Empreinte carbone estimée : {{ $event->carbon_footprint }} kg CO₂
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Informations pratiques supplémentaires -->
            @if($event->requirements || $event->what_to_bring || $event->accessibility_info)
                <div class="additional-info card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-clipboard-list text-primary me-2"></i>Informations Supplémentaires
                        </h3>
                        
                        @if($event->requirements)
                            <div class="mb-3">
                                <h5><i class="fas fa-exclamation-triangle text-warning me-2"></i>Prérequis</h5>
                                <ul class="list-unstyled ms-4">
                                    @foreach($event->requirements as $requirement)
                                        <li><i class="fas fa-check text-success me-2"></i>{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if($event->what_to_bring)
                            <div class="mb-3">
                                <h5><i class="fas fa-suitcase text-info me-2"></i>À apporter</h5>
                                <ul class="list-unstyled ms-4">
                                    @foreach($event->what_to_bring as $item)
                                        <li><i class="fas fa-arrow-right text-primary me-2"></i>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @if($event->accessibility_info)
                            <div class="mb-3">
                                <h5><i class="fas fa-wheelchair text-secondary me-2"></i>Accessibilité</h5>
                                <p class="ms-4">{{ $event->accessibility_info }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Inscription -->
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-body text-center">
                    <h4 class="card-title mb-3">Participer à l'événement</h4>
                    
                    <div class="price-display mb-3">
                        @if($event->is_free)
                            <div class="h2 text-success mb-0">Gratuit</div>
                        @else
                            <div class="h2 text-primary mb-0">{{ number_format($event->price, 2) }}€</div>
                            <small class="text-muted">par personne</small>
                        @endif
                    </div>
                    
                    @if($event->start_date < now()->toDateString())
                        <div class="alert alert-secondary">
                            <i class="fas fa-calendar-times me-2"></i>
                            Événement terminé
                        </div>
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="fas fa-times me-2"></i>Événement passé
                        </button>
                    @elseif($event->max_participants && $event->current_participants >= $event->max_participants)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Événement complet
                        </div>
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="fas fa-times me-2"></i>Plus de places disponibles
                        </button>
                    @elseif($event->registration_deadline && $event->registration_deadline < now())
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            Inscriptions fermées
                        </div>
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="fas fa-times me-2"></i>Inscriptions fermées
                        </button>
                    @else
                        @auth
                            @if($isRegistered)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Vous êtes inscrit à cet événement
                                </div>
                                <form method="POST" action="{{ route('events.unregister', $event) }}" class="mb-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-lg w-100" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir vous désinscrire ?')">
                                        <i class="fas fa-user-minus me-2"></i>Se désinscrire
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('events.register', $event) }}" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-calendar-plus me-2"></i>S'inscrire
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter pour s'inscrire
                            </a>
                        @endauth
                    @endif
                    
                    <div class="participants-info">
                        <small class="text-muted">
                            {{ $event->current_participants }} participant(s) inscrit(s)
                            @if($event->max_participants)
                                sur {{ $event->max_participants }} places
                            @endif
                        </small>
                    </div>
                    
                    @if($event->registration_deadline)
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Inscriptions jusqu'au {{ $event->registration_deadline->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact organisateur -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-user-tie text-primary me-2"></i>Organisateur
                    </h5>
                    <div class="organizer-info">
                        <h6 class="mb-2">{{ $event->organizer_name }}</h6>
                        @if($event->organizer_email)
                            <p class="mb-1">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $event->organizer_email }}">{{ $event->organizer_email }}</a>
                            </p>
                        @endif
                        @if($event->organizer_phone)
                            <p class="mb-0">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <a href="tel:{{ $event->organizer_phone }}">{{ $event->organizer_phone }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Partage -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-share-alt text-primary me-2"></i>Partager
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($event->title) }}" 
                           target="_blank" class="btn btn-outline-info btn-sm flex-fill">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm flex-fill" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Événements similaires -->
    @if($similarEvents->count() > 0)
        <section class="similar-events mt-5">
            <h2 class="section-title mb-4">
                <i class="fas fa-lightbulb text-warning me-2"></i>Événements Similaires
            </h2>
            
            <div class="row">
                @foreach($similarEvents as $similarEvent)
                    <div class="col-lg-4 mb-4">
                        <div class="card event-card h-100 shadow-sm border-0">
                            <div class="position-relative">
                                @if($similarEvent->image)
                                    <img src="{{ Storage::url($similarEvent->image) }}" alt="{{ $similarEvent->title }}" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="fas fa-calendar-alt text-muted" style="font-size: 2.5rem;"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-info">
                                        {{ App\Models\Event::getCategories()[$similarEvent->category] ?? $similarEvent->category }}
                                    </span>
                                </div>
                                
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('events.show', $similarEvent) }}" class="text-decoration-none text-dark">
                                        {{ $similarEvent->title }}
                                    </a>
                                </h5>
                                
                                <div class="event-details mb-3 flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <small>{{ $similarEvent->start_date->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <small>{{ $similarEvent->is_online ? 'En ligne' : $similarEvent->city }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="{{ route('events.show', $similarEvent) }}" class="btn btn-outline-primary">
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
</div>

<style>
.event-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
}

.card {
    border-radius: 15px;
}

.progress {
    border-radius: 10px;
}

.btn {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
}
</style>

<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Lien copié dans le presse-papiers !');
    });
}

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
