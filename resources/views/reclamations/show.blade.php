@extends('layouts.app')

@section('title', $reclamation->sujet . ' - ECO EVENT')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --border-radius: 15px;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    .hero-section {
        background: var(--primary-gradient);
        min-height: 40vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .reclamation-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    .response-card {
        background: #f8f9fa;
        border-left: 4px solid #28a745;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .star-rating {
        color: #ffc107;
        font-size: 1.5rem;
    }
    .star-rating-input {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .star-input {
        display: none;
    }
    .star-label {
        cursor: pointer;
        font-size: 2rem;
        color: #ddd;
        transition: color 0.2s;
    }
    .star-label:hover,
    .star-input:checked ~ .star-label {
        color: #ffc107;
    }
    .back-btn {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(220,53,69,0.4);
        color: white;
    }
    @media (max-width: 768px) {
        .hero-section { min-height: 30vh; }
        .star-label { font-size: 1.5rem; }
    }
    .card-negative {
    background-color: #f8d7da !important; /* rouge clair */
    color: #842029 !important; /* texte foncé */
}

.card-positive {
    background-color: #d1e7dd !important; /* vert clair */
    color: #0f5132 !important; /* texte foncé */
}

.card-neutre {
    background-color: #e2e3e5 !important; /* gris clair */
    color: #41464b !important; /* texte foncé */
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section text-white">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3" data-aos="fade-up">{{ $reclamation->sujet }}</h1>
                <p class="lead mb-0" data-aos="fade-up" data-aos-delay="200">{{ $reclamation->description }}</p>
                  @php
                        $sentimentColor = 'secondary'; // neutre par défaut
                        if(strtolower($reclamation->sentiment) === 'positif') $sentimentColor = 'success';
                        if(strtolower($reclamation->sentiment) === 'negatif') $sentimentColor = 'danger';
                    @endphp
                    <span class="badge bg-{{ $sentimentColor }} px-3 py-2 fw-bold">
                        {{ ucfirst($reclamation->sentiment) }}
                    </span>
            </div>
            <div class="col-lg-4 text-center">
                <div class="status-badge position-relative">
                    @if($reclamation->statut == 'traitee')
                        <span class="badge bg-success px-4 py-3 rounded-pill fw-bold fs-5">
                            <i class="fas fa-check me-2"></i>Traité
                        </span>
                    @elseif($reclamation->statut == 'en_cours')
                        <span class="badge bg-warning px-4 py-3 rounded-pill fw-bold fs-5">
                            <i class="fas fa-clock me-2"></i>En Cours
                        </span>
                    @else
                        <span class="badge bg-secondary px-4 py-3 rounded-pill fw-bold fs-5">
                            <i class="fas fa-hourglass-half me-2"></i>En Attente
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Back Navigation -->
<section class="py-3 bg-light">
    <div class="container">
        <a href="{{ route('reclamations.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Retour aux Réclamations
        </a>
    </div>
</section>

<!-- Reclamation Details -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10"> <!-- Sentiment -->
                          @php
    $cardClass = match(strtolower($reclamation->sentiment ?? 'neutre')) {
        'positif' => 'card-positive',
        'negatif' => 'card-negative',
        'neutre', null => 'card-neutre',
        default => 'card-neutre',
    };
@endphp
                <div class="reclamation-card p-4 mb-5 {{ $cardClass }}" data-aos="fade-up">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="fw-bold text-dark mb-3">{{ $reclamation->sujet }}</h3>
                             <h4>{{ $prioriteText }}</h4>
                            <p class="text-muted mb-2">{{ $reclamation->description }}</p>
                            
                           

                           

                            <div class="d-flex align-items-center mt-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.1rem; font-weight: bold;">
                                    {{ substr($reclamation->user->name ?? 'N/A', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $reclamation->user->name ?? 'Utilisateur Anonyme' }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $reclamation->created_at->format('d M Y à H:i') }}
                                        <i class="fas fa-clock ms-3 me-1"></i>{{ $reclamation->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mt-3">
                                <small class="text-muted">Note moyenne:</small><br>
                                @if($reclamation->avis->count() > 0)
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($reclamation->averageRating()))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">({{ number_format($reclamation->averageRating(), 1) }}/5 - {{ $reclamation->avis->count() }} avis)</small>
                                @else
                                    <p class="text-muted">Aucun avis</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Réponses Section -->
@if($reclamation->responses->count() > 0)
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="fw-bold text-center mb-5" data-aos="fade-up">
                    <i class="fas fa-reply text-success me-2"></i>Réponses de l'Administration
                </h2>
                
                @foreach($reclamation->responses as $response)
                    <div class="response-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1 text-success">
                                    {{ $response->user->name }}
                                    <span class="badge bg-success ms-2">Admin</span>
                                </h5>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $response->created_at->format('d M Y à H:i') }}
                                    ({{ $response->created_at->diffForHumans() }})
                                </small>
                            </div>
                        </div>
                        <div class="ms-5 ps-2">
                            <p class="mb-0" style="white-space: pre-wrap; font-size: 1.05rem; line-height: 1.7;">{{ $response->contenu }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Avis Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="fw-bold text-center mb-5" data-aos="fade-up">
                    <i class="fas fa-star text-warning me-2"></i>Donnez votre avis
                </h2>
                
                @auth
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8">
                        <form id="avis-form" action="{{ route('reclamations.avis.store', $reclamation) }}" method="POST" class="reclamation-card p-4" data-aos="fade-up">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold text-center d-block">Notez cette réclamation</label>
                                <div class="star-rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="note" value="{{ $i }}" class="star-input" required>
                                        <label for="star{{ $i }}" class="star-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer votre avis
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endauth

                @guest
                <div class="text-center py-5" data-aos="fade-up">
                    <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Connectez-vous pour donner votre avis</h5>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary mt-2">Se connecter</a>
                </div>
                @endguest

                <!-- Affichage des avis existants -->
                @if($reclamation->avis->count() > 0)
                    <div class="row mt-5">
                        <div class="col-12">
                            <h4 class="fw-bold mb-4" data-aos="fade-up">
                                Tous les avis ({{ $reclamation->avis->count() }})
                            </h4>
                            <div class="reclamation-card p-4" data-aos="fade-up">
                                @foreach($reclamation->avis as $avis)
                                    <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                            {{ substr($avis->user->name ?? 'N/A', 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $avis->user->name ?? 'Utilisateur Anonyme' }}</h6>
                                            <div class="star-rating" style="font-size: 1rem;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $avis->note)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <small class="text-muted">{{ $avis->created_at->format('d M Y') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    // Gestion des étoiles
    const starInputs = document.querySelectorAll('.star-input');
    const starLabels = document.querySelectorAll('.star-label');

    starLabels.forEach((label, index) => {
        label.addEventListener('mouseover', () => {
            starLabels.forEach((l, i) => {
                if (i >= index) {
                    l.style.color = '#ffc107';
                } else {
                    l.style.color = '#ddd';
                }
            });
        });

        label.addEventListener('mouseout', () => {
            const checkedInput = document.querySelector('.star-input:checked');
            if (checkedInput) {
                const checkedIndex = Array.from(starInputs).indexOf(checkedInput);
                starLabels.forEach((l, i) => {
                    if (i >= checkedIndex) {
                        l.style.color = '#ffc107';
                    } else {
                        l.style.color = '#ddd';
                    }
                });
            } else {
                starLabels.forEach((l) => {
                    l.style.color = '#ddd';
                });
            }
        });
    });
</script>
@endpush
