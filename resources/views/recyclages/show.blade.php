@extends('layouts.app')

@section('title', $recyclage->titre . ' - EcoEvent')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('recyclages.index') }}">Recyclages</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $recyclage->titre }}</li>
        </ol>
    </nav>

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
        <div class="col-md-8">
            <div class="card shadow-sm">
                @if($recyclage->typeRecyclage && $recyclage->typeRecyclage->couleur)
                    <div class="card-header" style="background-color: {{ $recyclage->typeRecyclage->couleur }}; color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @if($recyclage->typeRecyclage->icone)
                                    <i class="{{ $recyclage->typeRecyclage->icone }} me-2"></i>
                                @endif
                                {{ $recyclage->typeRecyclage->nom }}
                            </div>
                            <span class="badge bg-light text-dark">
                                {{ $recyclage->statut_formate }}
                            </span>
                        </div>
                    </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="h3 mb-2">{{ $recyclage->titre }}</h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user me-1"></i>Organisé par <strong>{{ $recyclage->user->name }}</strong>
                            </p>
                        </div>
                        
                        @auth
                            @if(auth()->id() === $recyclage->user_id || auth()->user()->isAdmin())
                                <div class="btn-group">
                                    <a href="{{ route('recyclages.edit', $recyclage) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <form action="{{ route('recyclages.destroy', $recyclage) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce recyclage ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $recyclage->description }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt text-danger me-2"></i>Lieu</h6>
                            <p class="mb-3">{{ $recyclage->lieu }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar text-primary me-2"></i>Date et horaires</h6>
                            <p class="mb-1"><strong>{{ $recyclage->date_collecte->format('d/m/Y') }}</strong></p>
                            <p class="text-muted mb-3">
                                De {{ $recyclage->heure_debut->format('H:i') }} à {{ $recyclage->heure_fin->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($recyclage->quantite_prevue || $recyclage->quantite_collectee)
                        <div class="mb-4">
                            <h6><i class="fas fa-weight text-success me-2"></i>Quantités</h6>
                            <div class="row">
                                @if($recyclage->quantite_prevue)
                                    <div class="col-md-6">
                                        <p class="mb-1">Quantité prévue : <strong>{{ $recyclage->quantite_prevue }} kg</strong></p>
                                    </div>
                                @endif
                                @if($recyclage->quantite_collectee)
                                    <div class="col-md-6">
                                        <p class="mb-1">Quantité collectée : <strong>{{ $recyclage->quantite_collectee }} kg</strong></p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($recyclage->quantite_prevue && $recyclage->quantite_collectee)
                                @php
                                    $pourcentage = ($recyclage->quantite_collectee / $recyclage->quantite_prevue) * 100;
                                @endphp
                                <div class="progress mt-2">
                                    <div class="progress-bar 
                                        @if($pourcentage >= 100) bg-success
                                        @elseif($pourcentage >= 75) bg-info
                                        @elseif($pourcentage >= 50) bg-warning
                                        @else bg-danger
                                        @endif" 
                                        role="progressbar" 
                                        style="width: {{ min($pourcentage, 100) }}%" 
                                        aria-valuenow="{{ $pourcentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        {{ number_format($pourcentage, 1) }}%
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($recyclage->notes)
                        <div class="mb-4">
                            <h6><i class="fas fa-sticky-note text-warning me-2"></i>Notes supplémentaires</h6>
                            <p class="text-muted">{{ $recyclage->notes }}</p>
                        </div>
                    @endif

                    <div class="border-top pt-3">
                        <small class="text-muted">
                            Créé le {{ $recyclage->created_at->format('d/m/Y à H:i') }}
                            @if($recyclage->created_at != $recyclage->updated_at)
                                • Modifié le {{ $recyclage->updated_at->format('d/m/Y à H:i') }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statut du recyclage</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge fs-6 
                            @if($recyclage->statut === 'planifie') bg-primary
                            @elseif($recyclage->statut === 'en_cours') bg-warning
                            @elseif($recyclage->statut === 'termine') bg-success
                            @else bg-secondary
                            @endif">
                            {{ $recyclage->statut_formate }}
                        </span>
                    </div>
                    
                    @if($recyclage->statut === 'planifie')
                        <p class="text-muted small">Ce recyclage est prévu pour le {{ $recyclage->date_collecte->format('d/m/Y') }}</p>
                    @elseif($recyclage->statut === 'en_cours')
                        <p class="text-muted small">Ce recyclage est actuellement en cours</p>
                    @elseif($recyclage->statut === 'termine')
                        <p class="text-muted small">Ce recyclage a été terminé avec succès</p>
                    @else
                        <p class="text-muted small">Ce recyclage a été annulé</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Type de recyclage</h5>
                </div>
                <div class="card-body">
                    @if($recyclage->typeRecyclage)
                        <div class="d-flex align-items-center mb-3">
                            @if($recyclage->typeRecyclage->icone)
                                <i class="{{ $recyclage->typeRecyclage->icone }} me-2" 
                                   style="color: {{ $recyclage->typeRecyclage->couleur ?? '#28a745' }}; font-size: 1.5rem;"></i>
                            @endif
                            <span class="badge" style="background-color: {{ $recyclage->typeRecyclage->couleur ?? '#28a745' }}; color: white;">
                                {{ $recyclage->typeRecyclage->nom }}
                            </span>
                        </div>
                        @if($recyclage->typeRecyclage->description)
                            <p class="text-muted small">{{ $recyclage->typeRecyclage->description }}</p>
                        @endif
                    @else
                        <p class="text-muted">Type non défini</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('recyclages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                        
                        @auth
                            @if(auth()->id() !== $recyclage->user_id && $recyclage->statut === 'planifie')
                                <button class="btn btn-success" onclick="alert('Fonctionnalité de participation à venir !')">
                                    <i class="fas fa-hand-paper me-2"></i>Participer
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter pour participer
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
