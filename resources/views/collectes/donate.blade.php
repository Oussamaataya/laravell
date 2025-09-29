@extends('layouts.app')

@section('title', 'Toutes les Campagnes - Gestion Collecte')

@push('styles')



@section('content')
<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 z-index-1050 shadow-lg" role="alert" style="z-index: 1050; max-width: 500px;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif



<!-- Campaign Details -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- Campaign Info -->
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                    <div class="card-body p-5">
                        <!-- Header with Image -->
                        <div class="text-center mb-5">
                            <div class="position-relative mx-auto mb-4" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #28a745, #20c997); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-seedling fa-4x text-white"></i>
                            </div>
                            <h2 class="fw-bold text-dark mb-2">{{ $campagne->nom }}</h2>
                            <p class="text-muted mb-1">Organisée par {{ $campagne->organisateur->name ?? 'Équipe Gestion Collecte' }}</p>
                            <small class="text-success fw-bold">
                                <i class="fas fa-calendar me-1"></i>Du {{ $campagne->date_debut->format('d M Y') }} au {{ $campagne->date_fin->format('d M Y') ?? 'Ouverte' }}
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Description du Projet</h5>
                            <p class="text-muted lead fs-6">{!! nl2br(e($campagne->description)) !!}</p>
                        </div>

                        <!-- Progress Cagnotte -->
                        <div id="cagnotte" class="mb-5">
                            <h5 class="fw-bold text-dark mb-4 text-center">Progrès de la Cagnotte</h5>
                            <div class="text-center mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h4 fw-bold text-success">{{ number_format($totalCollected, 2) }} €</span>
                                    <span class="h5 text-muted">Objectif: {{ number_format($campagne->montant_objectif, 2) }} €</span>
                                </div>
                                <div class="progress mb-4" style="height: 30px; border-radius: 20px; overflow: hidden; background: linear-gradient(90deg, #e9ecef, #dee2e6);">
                                    <div class="progress-bar bg-gradient progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: {{ min(100, $progress) }}%; background: linear-gradient(90deg, #28a745, #20c997) !important; border-radius: 20px;"
                                         aria-valuenow="{{ $totalCollected }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="{{ $campagne->montant_objectif }}">
                                        <div class="d-flex justify-content-between align-items-center h-100 px-4">
                                            <small class="text-white fw-bold">{{ round($progress) }}% Atteint</small>
                                            <small class="text-white fw-bold">{{ $numberOfDonors }} Donateurs</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center g-3">
                                    <div class="col-6">
                                        <i class="fas fa-fire fa-2x text-warning mb-2"></i>
                                        <p class="small text-muted mb-1">Projet Actif</p>
                                        <small class="text-success fw-bold">{{ $campagne->statut }}</small>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-clock fa-2x text-info mb-2"></i>
                                        <p class="small text-muted mb-1">Temps Restant</p>
                                        <small class="text-primary fw-bold">{{ $campagne->date_fin ? $campagne->date_fin->diffForHumans() : 'Ouverte Indéfiniment' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Donations -->
                        <div class="mb-5">
                            <h5 class="fw-bold text-dark mb-3">Contributions Récentes</h5>
                            @if($campagne->collectes->isEmpty())
                                <div class="text-center p-4 bg-white rounded-3">
                                    <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Soyez le premier à contribuer !</p>
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($campagne->collectes as $collecte)
                                        <div class="list-group-item px-0 py-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="fas fa-user text-success"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">{{ $collecte->utilisateur->name ?? 'Anonyme' }}</small>
                                                        @if($collecte->message)
                                                            <p class="mb-0 small text-dark mt-1">"{{ Str::limit($collecte->message, 50) }}"</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="badge bg-success rounded-pill px-3 py-2 fs-6">
                                                    +{{ number_format($collecte->montant, 2) }} €
                                                </span>
                                            </div>
                                            <small class="text-muted d-block mt-1">{{ $collecte->created_at->diffForHumans() }}</small>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('collectes.show', $campagne) }}" class="text-decoration-none small text-primary">Voir toutes les contributions</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donation Form Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-lg border-0 rounded-4 h-100 sticky-top" style="top: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-center mb-4 text-success">
                            <i class="fas fa-piggy-bank me-2"></i>Ma Contribution
                        </h5>

                        <form action="{{ route('collectes.donate', $campagne) }}" method="POST" id="donationForm">
                            @csrf
                            <!-- Suggested Amounts -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3 text-center">Montants Suggérés</h6>
                                <div class="row g-2">
                                    <div class="col-3">
                                        <button type="button" class="btn btn-outline-success w-100 py-3 rounded-3 suggested-amount fs-6" data-amount="10">
                                            <i class="fas fa-coins mb-1 d-block"></i>10 €
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" class="btn btn-outline-success w-100 py-3 rounded-3 suggested-amount fs-6" data-amount="25">
                                            <i class="fas fa-coins mb-1 d-block"></i>25 €
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" class="btn btn-outline-success w-100 py-3 rounded-3 suggested-amount fs-6" data-amount="50">
                                            <i class="fas fa-gem mb-1 d-block"></i>50 €
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" class="btn btn-outline-success w-100 py-3 rounded-3 suggested-amount fs-6" data-amount="100">
                                            <i class="fas fa-crown mb-1 d-block"></i>100 €
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Amount -->
                            <div class="input-group mb-4 bg-white rounded-3 shadow-sm p-3">
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="fas fa-euro-sign text-success fs-5"></i>
                                </span>
                                <input type="number" name="montant" id="montant" class="form-control border-0 text-center fs-5 fw-bold" placeholder="Autre montant" min="1" step="0.01" required>
                            </div>

                            <!-- Message -->
                            <div class="mb-4">
                                <label for="message" class="form-label fw-bold text-muted small">Message Optionnel</label>
                                <textarea class="form-control rounded-3 border-success" id="message" name="message" rows="3" placeholder="Laissez un message pour encourager la campagne... (max 255 caractères)" maxlength="255"></textarea>
                                <div class="form-text">Votre message sera visible publiquement.</div>
                            </div>

                            <!-- Payment Info -->
                            <div class="alert alert-info rounded-3 mb-4">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>
                                <strong>Paiement Sécurisé</strong> via carte bancaire. Vos données sont protégées.
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold position-relative overflow-hidden" style="background: linear-gradient(135deg, #28a745, #20c997); border: none; box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);">
                                <i class="fas fa-credit-card me-2"></i>Effectuer le Don Sécurisé
                                <span class="position-absolute top-0 start-0 w-100 h-100 opacity-0 transition-opacity" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);"></span>
                            </button>
                        </form>

                        <!-- Trust Badges -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <i class="fas fa-lock fa-2x text-primary mb-1"></i>
                                    <small class="text-muted d-block">Sécurisé</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-1"></i>
                                    <small class="text-muted d-block">Vérifié</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-heart fa-2x text-danger mb-1"></i>
                                    <small class="text-muted d-block">Impact</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h3 class="fw-bold mb-4 text-dark">Pourquoi Contribuer ?</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <i class="fas fa-tree fa-3x text-success mb-3"></i>
                            <h5 class="fw-bold mb-2">Impact Environnemental</h5>
                            <p class="text-muted">Votre don soutient des initiatives écologiques durables.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5 class="fw-bold mb-2">Communauté Solidaire</h5>
                            <p class="text-muted">Rejoignez des milliers de contributeurs engagés.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <i class="fas fa-star fa-3x text-warning mb-3"></i>
                            <h5 class="fw-bold mb-2">Transparence Totale</h5>
                            <p class="text-muted">Suivez l'utilisation de chaque euro collecté.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container text-center">
        <h4 class="mb-3">Prêt à Faire la Différence ?</h4>
        <p class="lead mb-4">Chaque don compte pour un avenir meilleur.</p>
        <a href="#cagnotte" class="btn btn-success btn-lg px-5 rounded-pill">
            <i class="fas fa-donate me-2"></i>Donner Maintenant
        </a>
        <p class="mt-4 mb-0 small">&copy; 2024 Gestion Collecte. Tous droits réservés. | <a href="{{ route('collectes.show', $campagne) }}" class="text-white-50">Retour à la Campagne</a></p>
    </div>
</footer>

@push('scripts')
<script>
    AOS.init({ duration: 1000, once: true });

    // Suggested amounts
    document.addEventListener('DOMContentLoaded', function() {
        const suggestedButtons = document.querySelectorAll('.suggested-amount');
        const amountInput = document.getElementById('montant');

        suggestedButtons.forEach(button => {
            button.addEventListener('click', function() {
                const amount = this.dataset.amount;
                amountInput.value = amount;
                suggestedButtons.forEach(btn => {
                    btn.classList.remove('btn-success', 'shadow-lg');
                    btn.classList.add('btn-outline-success');
                });
                this.classList.remove('btn-outline-success');
                this.classList.add('btn-success', 'shadow-lg');
            });
        });

        // Submit button shimmer
        const submitBtn = document.querySelector('button[type="submit"]');
        const shimmer = submitBtn.querySelector('span.position-absolute');
        submitBtn.addEventListener('mouseenter', function() {
            shimmer.style.opacity = '1';
            shimmer.style.left = '-100%';
            setTimeout(() => {
                shimmer.style.left = '100%';
            }, 10);
        });
        submitBtn.addEventListener('mouseleave', function() {
            shimmer.style.opacity = '0';
            shimmer.style.left = '-100%';
        });

        // Smooth scroll to cagnotte
        document.querySelectorAll('a[href="#cagnotte"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('#cagnotte').scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Form validation
        const form = document.getElementById('donationForm');
        form.addEventListener('submit', function(e) {
            const montant = document.getElementById('montant').value;
            if (!montant || montant < 1) {
                e.preventDefault();
                alert('Veuillez entrer un montant valide (minimum 1 €).');
            }
        });
    });
</script>
@endpush

@endsection
