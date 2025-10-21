@extends('layouts.base')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Bouton retour -->
            <a href="{{ route('events.my-registrations') }}" class="btn btn-sm btn-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Retour à mes inscriptions
            </a>

            <!-- Billet électronique -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">🎫 Billet Électronique</h2>
                </div>
                
                <div class="card-body p-5">
                    <!-- Informations de l'événement -->
                    <div class="text-center mb-4">
                        <h3 class="text-primary">{{ $registration->event->title }}</h3>
                        <p class="text-muted mb-1">
                            <i class="fas fa-calendar"></i> 
                            {{ \Carbon\Carbon::parse($registration->event->start_date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </p>
                        <p class="text-muted mb-1">
                            <i class="fas fa-clock"></i> 
                            {{ $registration->event->start_time }} - {{ $registration->event->end_time }}
                        </p>
                        @if(!$registration->event->is_online)
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> 
                                {{ $registration->event->location }}
                            </p>
                        @else
                            <p class="text-muted">
                                <i class="fas fa-video"></i> Événement en ligne
                            </p>
                        @endif
                    </div>

                    <hr>

                    <!-- Informations du participant -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-secondary">Participant</h5>
                            <p class="mb-1"><strong>{{ $registration->user->name }}</strong></p>
                            <p class="mb-0 text-muted">{{ $registration->user->email }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h5 class="text-secondary">N° de billet</h5>
                            <p class="mb-1"><strong>{{ $registration->ticket_code }}</strong></p>
                            <p class="mb-0">
                                @if($registration->ticket_status === 'active')
                                    <span class="badge badge-success">Actif</span>
                                @elseif($registration->ticket_status === 'used')
                                    <span class="badge badge-info">Utilisé</span>
                                @else
                                    <span class="badge badge-danger">Annulé</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center mb-4">
                        <div class="p-4 bg-light rounded">
                            @if($registration->qr_code_path)
                                <img src="{{ Storage::url($registration->qr_code_path) }}" 
                                     alt="QR Code" 
                                     class="img-fluid mb-3"
                                     style="max-width: 300px;">
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    QR Code en cours de génération...
                                </div>
                            @endif
                            <p class="text-muted small mb-0">
                                Présentez ce QR Code à l'entrée de l'événement
                            </p>
                        </div>
                    </div>

                    <!-- Statut du check-in -->
                    @if($registration->checked_in_at)
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle"></i>
                            <strong>Billet validé</strong><br>
                            <small>Le {{ $registration->checked_in_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    @endif

                    <!-- Instructions -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Instructions importantes</h6>
                        <ul class="mb-0 small">
                            <li>Présentez ce billet (imprimé ou sur votre téléphone) à l'entrée</li>
                            <li>Le QR Code sera scanné pour valider votre présence</li>
                            <li>Un billet ne peut être utilisé qu'une seule fois</li>
                            <li>Arrivez quelques minutes avant le début de l'événement</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-primary mr-2">
                            <i class="fas fa-print"></i> Imprimer le billet
                        </button>
                        @if($registration->qr_code_path)
                            <a href="{{ Storage::url($registration->qr_code_path) }}" 
                               download="{{ $registration->ticket_code }}.png"
                               class="btn btn-outline-primary">
                                <i class="fas fa-download"></i> Télécharger QR Code
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Footer du billet -->
                <div class="card-footer text-center text-muted small">
                    <p class="mb-0">Billet généré le {{ $registration->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Description de l'événement -->
            @if($registration->event->description)
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">À propos de l'événement</h5>
                        <p class="card-text">{{ $registration->event->description }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Style pour l'impression -->
<style>
@media print {
    .btn, .alert-info, nav, footer {
        display: none !important;
    }
    .card {
        border: 2px solid #000;
        box-shadow: none !important;
    }
}
</style>
@endsection
