@extends('layouts.app')

@section('title', 'Recyclages - EcoEvent')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-1">Recyclages</h1>
                    <p class="text-muted">Découvrez les initiatives de recyclage près de chez vous</p>
                </div>
                @auth
                    <a href="{{ route('recyclages.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Organiser un Recyclage
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter pour organiser
                    </a>
                @endauth
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($recyclages as $recyclage)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($recyclage->typeRecyclage && $recyclage->typeRecyclage->couleur)
                        <div class="card-header" style="background-color: {{ $recyclage->typeRecyclage->couleur }}; color: white;">
                            @if($recyclage->typeRecyclage->icone)
                                <i class="{{ $recyclage->typeRecyclage->icone }} me-2"></i>
                            @endif
                            {{ $recyclage->typeRecyclage->nom }}
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $recyclage->titre }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($recyclage->description, 100) }}</p>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $recyclage->lieu }}
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>{{ $recyclage->date_collecte->format('d/m/Y') }}
                            </small><br>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $recyclage->heure_debut->format('H:i') }} - {{ $recyclage->heure_fin->format('H:i') }}
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge 
                                @if($recyclage->statut === 'planifie') bg-primary
                                @elseif($recyclage->statut === 'en_cours') bg-warning
                                @elseif($recyclage->statut === 'termine') bg-success
                                @else bg-secondary
                                @endif">
                                {{ $recyclage->statut_formate }}
                            </span>
                            
                            @if($recyclage->quantite_prevue)
                                <small class="text-muted">
                                    <i class="fas fa-weight me-1"></i>{{ $recyclage->quantite_prevue }} kg prévus
                                </small>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Par {{ $recyclage->user->name }}
                            </small>
                            <a href="{{ route('recyclages.show', $recyclage) }}" class="btn btn-outline-primary btn-sm">
                                Voir détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-recycle text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">Aucun recyclage disponible</h3>
                    <p class="text-muted">Soyez le premier à organiser une initiative de recyclage !</p>
                    @auth
                        <a href="{{ route('recyclages.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Organiser un Recyclage
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Se connecter pour organiser
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    @if($recyclages->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $recyclages->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
