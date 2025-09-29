@extends('layouts.app')

@section('title', $campagne->nom . ' - Gestion Collecte')

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Hero Section -->
        <div class="row justify-content-center mb-5" data-aos="fade-up">
            <div class="col-md-8 text-center">
                <h1 class="section-title text-uppercase mb-3">{{ $campagne->nom }}</h1>
                <p class="lead text-muted">Organisée par {{ $campagne->organisateur->name ?? 'Gestion Collecte' }} | Du {{ $campagne->date_debut->format('d/m/Y') }} au {{ $campagne->date_fin->format('d/m/Y') ?? 'Ouverte' }}</p>
            </div>
        </div>

        <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-img-top position-relative" style="height: 300px;">
                        <img src="{{ $campagne->image ?? asset('images/default-campagne.jpg') }}" 
                             class="img-fluid w-100 h-100 object-fit-cover" 
                             alt="{{ $campagne->nom }}">
                        <div class="position-absolute bottom-0 start-0 p-3 bg-dark bg-opacity-50 text-white">
                            <span class="badge bg-success fs-6">{{ ucfirst($campagne->statut) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="text-uppercase mb-3">Description</h5>
                            <p class="text-muted">{!! nl2br(e($campagne->description)) !!}</p>
                        </div>

                        <!-- Progress Section -->
                        <div class="mb-4">
                            <h5 class="text-uppercase mb-3">Progrès de la Collecte</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Collecté: {{ number_format($totalCollected, 2) }} €</span>
                                <span class="text-success fw-bold">{{ number_format($progress, 1) }}% atteint</span>
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: {{ $progress }}%" 
                                     aria-valuenow="{{ $progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($totalCollected, 2) }} / {{ number_format($campagne->montant_objectif, 2) }} €
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted">Objectif</small>
                                    <div class="fw-bold text-primary">{{ number_format($campagne->montant_objectif, 2) }} €</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Contributeurs</small>
                                    <div class="fw-bold text-success">{{ $numberOfDonors }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Call to Action -->
                        <div class="text-center mb-4">
                            @auth
                                <a href="#donate" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-heart me-2"></i>Contribuer Maintenant
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-sign-in-alt me-2"></i>Connectez-vous pour Contribuer
                                </a>
                            @endauth
                        </div>

                        <!-- Donation Section (Cagnotte) -->
                        <div id="donate" class="mb-5">
                            <h5 class="text-uppercase mb-4 text-center">Faire un Don</h5>
                            @auth
                                <form action="{{ route('collectes.donate', $campagne) }}" method="POST" class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    @csrf
                                    <div class="card-body p-5">
                                        <!-- Progress Visualization -->
                                        <div class="text-center mb-4">
                                            <div class="progress mb-3" style="height: 25px; border-radius: 15px; overflow: hidden;">
                                                <div class="progress-bar bg-gradient" role="progressbar" style="width: {{ $progress }}%; background: linear-gradient(90deg, #28a745, #20c997) !important; border-radius: 15px;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                    <small class="text-white fw-bold">{{ number_format($progress, 1) }}% - {{ number_format($totalCollected, 2) }} / {{ number_format($campagne->montant_objectif, 2) }} €</small>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-0">Votre don nous rapproche de l'objectif !</p>
                                        </div>

                                        <!-- Suggested Amounts -->
                                        <div class="row g-3 mb-4 text-center">
                                            <div class="col-3">
                                                <button type="button" class="btn btn-outline-success btn-lg w-100 p-3 rounded-4 suggested-amount" data-amount="10">10 €</button>
                                            </div>
                                            <div class="col-3">
                                                <button type="button" class="btn btn-outline-success btn-lg w-100 p-3 rounded-4 suggested-amount" data-amount="25">25 €</button>
                                            </div>
                                            <div class="col-3">
                                                <button type="button" class="btn btn-outline-success btn-lg w-100 p-3 rounded-4 suggested-amount" data-amount="50">50 €</button>
                                            </div>
                                            <div class="col-3">
                                                <button type="button" class="btn btn-outline-success btn-lg w-100 p-3 rounded-4 suggested-amount" data-amount="100">100 €</button>
                                            </div>
                                        </div>

                                        <!-- Custom Amount -->
                                        <div class="input-group mb-4">
                                            <span class="input-group-text bg-white border-success"><i class="fas fa-euro-sign text-success"></i></span>
                                            <input type="number" name="montant" id="montant" class="form-control form-control-lg border-success rounded-end" placeholder="Montant personnalisé (€)" min="1" step="0.01" required>
                                            <button class="btn btn-success" type="button"><i class="fas fa-magic"></i></button>
                                        </div>

                                        <!-- Optional Message -->
                                        <div class="mb-4">
                                            <label for="message" class="form-label fw-bold">Message d'accompagnement (optionnel)</label>
                                            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Ajoutez un message personnel pour encourager la campagne..."></textarea>
                                        </div>

                                        <!-- Security & Impact -->
                                        <div class="row mb-4">
                                            <div class="col-6 text-center">
                                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                                <p class="small text-muted mb-0">Paiement Sécurisé</p>
                                            </div>
                                            <div class="col-6 text-center">
                                                <i class="fas fa-leaf fa-2x text-success mb-2"></i>
                                                <p class="small text-muted mb-0">Impact Immédiat</p>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold" style="background: linear-gradient(135deg, #28a745, #20c997); border: none; box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);">
                                            <i class="fas fa-donate me-2"></i>Faire un Don Sécurisé
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="text-center p-5 bg-light rounded-4">
                                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Connectez-vous pour faire un don</h5>
                                    <p class="text-muted mb-4">Votre contribution est importante ! Créez un compte ou connectez-vous pour soutenir cette campagne.</p>
                                    <a href="{{ route('login') }}" class="btn btn-success">Se Connecter</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Recent Collectes -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h6 class="text-uppercase mb-0">Contributions Récentes</h6>
                    </div>
                    <div class="card-body">
                        @if($recentCollectes->isEmpty())
                            <p class="text-muted text-center">Aucune contribution récente.</p>
                        @else
                            <ul class="list-unstyled">
                                @foreach($recentCollectes as $collecte)
                                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span class="text-muted">
                                            {{ $collecte->utilisateur ? $collecte->utilisateur->name : 'Anonyme' }}
                                        </span>
                                        <span class="fw-bold text-success">
                                            +{{ number_format($collecte->montant, 2) }} €
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <small class="text-muted d-block text-center mt-2">
                                <a href="#" class="text-decoration-none">Voir toutes les contributions</a>
                            </small>
                        @endif
                    </div>
                </div>

                <!-- Share Section -->
                <div class="card shadow-sm border-0 mt-4 h-100">
                    <div class="card-header bg-light">
                        <h6 class="text-uppercase mb-0">Partager cette Campagne</h6>
                    </div>
                    <div class="card-body text-center">
                        <ul class="list-unstyled d-flex justify-content-center gap-3 mb-0">
                            <li><a href="#" class="text-secondary"><i class="fab fa-facebook-f fs-4"></i></a></li>
                            <li><a href="#" class="text-secondary"><i class="fab fa-twitter fs-4"></i></a></li>
                            <li><a href="#" class="text-secondary"><i class="fab fa-linkedin-in fs-4"></i></a></li>
                            <li><a href="#" class="text-secondary"><i class="fab fa-instagram fs-4"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
