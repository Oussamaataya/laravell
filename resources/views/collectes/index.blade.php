@extends('layouts.app')

@section('title', 'Toutes les Campagnes - Gestion Collecte')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
        animation: wave 20s linear infinite;
    }
    @keyframes wave {
        0% { transform: translateX(0); }
        100% { transform: translateX(-1000px); }
    }
    .campaign-card {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        box-shadow: var(--card-shadow);
    }
    .campaign-card:hover {
        transform: translateY(-15px) rotateX(5deg);
        box-shadow: var(--hover-shadow);
    }
    .campaign-image {
        height: 280px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .campaign-image i {
        font-size: 4rem;
        color: #6c757d;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
    }
    .progress-custom {
        height: 12px;
        border-radius: 20px;
        background: linear-gradient(90deg, #e9ecef, #dee2e6);
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    .progress-bar-custom {
        border-radius: 20px;
        transition: width 1s ease-out, background 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .progress-bar-custom::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
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
        color: #28a745 !important;
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(40,167,69,0.3);
    }
    .stats-card {
        background: linear-gradient(135deg, var(--primary-gradient));
        border-radius: var(--border-radius);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: scale(1.05);
    }
    .donate-btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 50px;
        padding: 12px 24px;
        font-weight: bold;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .donate-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    .donate-btn:hover::before {
        left: 100%;
    }
    .donate-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(40,167,69,0.4);
    }
    @media (max-width: 768px) {
        .hero-section h1 { font-size: 2.5rem; }
        .campaign-card:hover { transform: translateY(-5px); }
    }
</style>
@endpush

@section('content')
<section class="py-4 bg-light">
    <div class="container">

                <!-- Campaigns Grid -->
                <div class="row g-4">
                    @foreach($campagnes as $campagne)
                        <div class="col-lg-4 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                            <div class="campaign-card">
                                <!-- Status Badge -->
                                <div class="position-absolute top-3 end-3 z-2">
                                    <span class="badge status-badge {{ $campagne->statut == 'active' ? 'bg-success' : ($campagne->statut == 'terminée' ? 'bg-secondary' : 'bg-warning') }} px-3 py-2 rounded-pill fw-bold">
                                        {{ ucfirst($campagne->statut) }}
                                    </span>
                                </div>

                                <!-- Image Section -->
                                <div class="campaign-image">
                                    <i class="fas fa-seedling"></i>
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-70 text-white">
                                        <small class="fw-bold">Objectif: {{ number_format($campagne->montant_objectif, 0) }} €</small>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <!-- Title and Date -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="fw-bold text-truncate" style="color: #28a745; font-size: 1.2rem; max-width: 80%;">{{ $campagne->nom }}</h5>
                                        <small class="text-muted">{{ $campagne->date_debut->format('d M Y') }}</small>
                                    </div>
                                    <p class="text-muted mb-4" style="height: 60px; overflow: hidden;">{{ Str::limit($campagne->description, 100) }}</p>

                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        @php
                                            $totalCollected = $campagne->collectes_sum_montant ?? 0;
                                            $progress = $campagne->montant_objectif > 0 ? min(100, ($totalCollected / $campagne->montant_objectif) * 100) : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between small text-muted mb-2">
                                            <span>{{ number_format($totalCollected, 0) }} / {{ number_format($campagne->montant_objectif, 0) }} €</span>
                                            <span class="text-success fw-bold">{{ round($progress) }}%</span>
                                        </div>
                                        <div class="progress-custom">
                                            <div class="progress-bar progress-bar-custom bg-success"
                                                 role="progressbar"
                                                 style="width: {{ $progress }}%"
                                                 aria-valuenow="{{ $totalCollected }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="{{ $campagne->montant_objectif }}">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 small">
                                            <span><i class="fas fa-users text-primary me-1"></i>{{ $campagne->collectes_count ?? 0 }} donateurs</span>
                                            <span><i class="fas fa-clock text-secondary me-1"></i>{{ $campagne->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                   <!-- Actions -->
<div class="d-flex justify-content-center gap-2">
    @if($campagne->statut == 'active')
        <a href="{{ route('collectes.donate.form', $campagne) }}" 
           class="donate-btn text-white rounded-pill px-3">
            <i class="fas fa-donate me-1"></i> Donner
        </a>
    @endif
</div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($campagnes->hasPages())
                    <nav class="mt-5" aria-label="Pagination">
                        <ul class="pagination justify-content-center">
                            {{ $campagnes->appends(request()->query())->links() }}
                        </ul>
                    </nav>
                @endif
            
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Gestion Collecte. Tous droits réservés.</p>
        </div>
    </footer>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });
</script>
@endpush
