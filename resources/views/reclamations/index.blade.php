@extends('layouts.app')

@section('title', 'Toutes les Réclamations - ECO EVENT')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

@php
    use App\Models\Reclamation;
@endphp
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
        --border-radius: 15px;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    .hero-section {
        background: var(--primary-gradient);
        min-height: 60vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>') repeat-x;
        animation: wave 20s linear infinite;
    }
    @keyframes wave {
        0% { transform: translateX(0); }
        100% { transform: translateX(-1000px); }
    }
    .reclamation-card {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        box-shadow: var(--card-shadow);
    }
    .reclamation-card:hover {
        transform: translateY(-15px) rotateX(5deg);
        box-shadow: var(--hover-shadow);
    }
    .reclamation-image {
        height: 200px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .reclamation-image i {
        font-size: 3rem;
        color: #dc3545;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
    }
    .status-badge {
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .filter-btn {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .filter-btn:hover, .filter-btn.active {
        background: white !important;
        color: #dc3545 !important;
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(220,53,69,0.3);
    }
    .stats-card {
        background: linear-gradient(135deg, var(--primary-gradient));
        border-radius: var(--border-radius);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: scale(1.05);
    }
    .view-btn {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: bold;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .view-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    .view-btn:hover::before {
        left: 100%;
    }
    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(220,53,69,0.4);
    }
    @media (max-width: 768px) {
        .hero-section h1 { font-size: 2.5rem; }
        .reclamation-card:hover { transform: translateY(-5px); }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section text-white">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">Toutes les Réclamations</h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="200">Découvrez les réclamations traitées avec succès et les solutions apportées à nos utilisateurs.</p>
                <div class="d-flex gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="400">
                    <a href="#search" class="btn btn-light btn-lg px-4 rounded-pill">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-balance-scale fa-10x opacity-75" data-aos="zoom-in" data-aos-delay="600"></i>
            </div>
        </div>
    </div>
</section>


<!-- Search and Filter -->
<section class="py-4">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher par sujet ou description..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Toutes les Statuts</option>
                    <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                    <option value="en_cours" {{ request('status') == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                    <option value="traitee" {{ request('status') == 'traitee' ? 'selected' : '' }}>Traitées</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>

        @auth
        <div class="row mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#createReclamationModal">
                    <i class="fas fa-plus me-2"></i>Nouvelle Réclamation
                </button>
            </div>
        </div>
        @endauth
    </div>
</section>

<!-- Create Reclamation Modal -->
<div class="modal fade" id="createReclamationModal" tabindex="-1" aria-labelledby="createReclamationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createReclamationModalLabel">
                    <i class="fas fa-plus me-2 text-success"></i>Nouvelle Réclamation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reclamations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sujet" class="form-label fw-bold">Sujet de la réclamation</label>
                        <input type="text" name="sujet" id="sujet" class="form-control @error('sujet') is-invalid @enderror" placeholder="Décrivez brièvement le sujet..." value="{{ old('sujet') }}" required>
                        @error('sujet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description détaillée</label>
                        <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Expliquez en détail votre réclamation..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Soumettre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reclamations Grid -->
<section class="py-5">
    <div class="container">
        @if($reclamations->count() > 0)
            <div class="row g-4">
                @foreach($reclamations as $reclamation)
                    <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s;" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="reclamation-card h-100">
                            <!-- Status Badge -->
                            <div class="position-absolute top-3 end-3 z-2">
                                @if($reclamation->statut == 'traitee')
                                    <span class="badge status-badge bg-success px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-check me-1"></i>Traitee
                                    </span>
                                @elseif($reclamation->statut == 'en_cours')
                                    <span class="badge status-badge bg-warning px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-clock me-1"></i>En Cours
                                    </span>
                                @else
                                    <span class="badge status-badge bg-secondary px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-hourglass-half me-1"></i>En Attente
                                    </span>
                                @endif
                            </div>

                            <!-- Image Section -->
                            <div class="reclamation-image">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>

                            <div class="p-4">
                                <!-- Title and Date -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="fw-bold text-truncate" style="color: #dc3545; font-size: 1.2rem; max-width: 80%;">
                                        {{ $reclamation->sujet }}
                                    </h5>
                                    <small class="text-muted">{{ $reclamation->created_at ? $reclamation->created_at->format('d M Y') : 'N/A' }}</small>
                                </div>

                                <p class="text-muted mb-4" style="height: 80px; overflow: hidden;">
                                    {{ Str::limit($reclamation->description, 120) }}
                                </p>

                                <!-- User Info -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                        {{ substr($reclamation->user->name ?? 'N/A', 0, 1) }}
                                    </div>
                                    <div>
                                        <small class="fw-bold text-dark">{{ $reclamation->user->name ?? 'Utilisateur Anonyme' }}</small><br>
                                        <small class="text-muted">{{ $reclamation->created_at ? $reclamation->created_at->diffForHumans() : 'Récemment' }}</small>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('reclamations.show', $reclamation) }}" class="view-btn text-white">
                                        <i class="fas fa-comments me-1"></i> Voir Avis
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($reclamations->hasPages())
                <nav class="mt-5" aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        {{ $reclamations->appends(request()->query())->links() }}
                    </ul>
                </nav>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-5x text-muted mb-4"></i>
                <h4 class="text-muted">Aucune réclamation trouvée</h4>
                <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
            </div>
        @endif
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; 2024 ECO EVENT. Tous droits réservés.</p>
    </div>
</footer>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    // Simple search/filter JS if needed
    document.querySelector('form').addEventListener('submit', function(e) {
        // Preserve existing functionality
    });
</script>
@endpush
