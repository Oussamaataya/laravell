@extends('layouts.app')

@section('title', 'Accueil - Gestion Collecte')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5" style="background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">Bienvenue sur Gestion Collecte</h1>
        <p class="lead mb-4">Rejoignez-nous pour soutenir des causes importantes. Découvrez nos campagnes actives et contribuez à un monde meilleur.</p>
        <a href="{{ route('collectes.index') }}" class="btn btn-light btn-lg px-4 py-2 fw-semibold">Voir Toutes les Campagnes</a>
    </div>
</section>

<!-- Search Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h3 fw-semibold text-center mb-4">Rechercher une Campagne</h2>
                        <form method="GET" action="{{ route('home') }}" class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom de la campagne..." class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100">Rechercher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaigns Grid -->
        <div class="row g-4">
            @forelse($campagnes as $campagne)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <!-- Placeholder Image -->
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-muted">Image Campagne</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $campagne->nom }}</h5>
                            <p class="card-text text-muted" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ $campagne->description }}</p>
                            
                            <!-- Progress Bar -->
                            <div class="mb-3">
                                @php
                                    $totalCollected = $campagne->collectes_sum_montant ?? 0;
                                    $progress = $campagne->montant_objectif > 0 ? ($totalCollected / $campagne->montant_objectif) * 100 : 0;
                                @endphp
                                <div class="d-flex justify-content-between small text-muted mb-2">
                                    <span>{{ number_format($totalCollected, 2) }} / {{ number_format($campagne->montant_objectif, 2) }} €</span>
                                    <span>{{ round($progress) }}% atteint</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="d-flex justify-content-between small text-muted mb-3">
                                <span>{{ $campagne->collectes_count ?? 0 }} donateurs</span>
                                <span>Par {{ $campagne->organisateur->name ?? 'Organisateur' }}</span>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('collectes.show', $campagne) }}" class="btn btn-primary w-100">Contribuer Maintenant</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">Aucune campagne active pour le moment. Revenez bientôt !</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <nav class="mt-5" aria-label="Pagination des campagnes">
            {{ $campagnes->links() }}
        </nav>
    </div>
</section>
@endsection
