@extends('layouts.back')

@section('title', 'Détails de la Campagne - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Détails de la Campagne #{{ $campagne->id }}</h4>
                        <p class="card-description">Informations complètes sur cette campagne.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Informations de la Campagne</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>ID :</strong> {{ $campagne->id }}</p>
                                        <p><strong>Nom :</strong> {{ $campagne->nom }}</p>
                                        <p><strong>Description :</strong> {{ $campagne->description ?? 'Aucune description' }}</p>
                                        <p><strong>Montant Objectif :</strong> {{ number_format($campagne->montant_objectif, 2) }} €</p>
                                        <p><strong>Montant Actuel :</strong> {{ number_format($campagne->montant_actuel, 2) }} €</p>
                                        <p><strong>Progression :</strong> {{ $campagne->montant_objectif > 0 ? number_format(($campagne->montant_actuel / $campagne->montant_objectif) * 100, 1) : 0 }} %</p>
                                        <p><strong>Statut :</strong>
                                            <span class="badge badge-{{ $campagne->statut === 'active' ? 'success' : ($campagne->statut === 'brouillon' ? 'warning' : ($campagne->statut === 'terminée' ? 'info' : 'danger')) }}">
                                                {{ ucfirst($campagne->statut) }}
                                            </span>
                                        </p>
                                        <p><strong>Date de Début :</strong> {{ $campagne->date_debut?->format('d/m/Y') ?? 'N/A' }}</p>
                                        <p><strong>Date de Fin :</strong> {{ $campagne->date_fin?->format('d/m/Y') ?? 'N/A' }}</p>
                                        <p><strong>Créée le :</strong> {{ $campagne->created_at->format('d/m/Y H:i') }}</p>
                                        <p><strong>Mise à jour le :</strong> {{ $campagne->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Organisateur et Collectes</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Organisateur :</strong> {{ $campagne->organisateur->name ?? 'N/A' }}</p>
                                        <p><strong>Email Organisateur :</strong> {{ $campagne->organisateur->email ?? 'N/A' }}</p>
                                        <p><strong>Rôle Organisateur :</strong> {{ ucfirst($campagne->organisateur->role ?? 'N/A') }}</p>
                                        <hr>
                                        <p><strong>Nombre de Collectes :</strong> {{ $campagne->collectes->count() }}</p>
                                        <p><strong>Montant Total Collecté :</strong> {{ number_format($campagne->collectes->sum('montant'), 2) }} €</p>
                                        @if($campagne->collectes->count() > 0)
                                            <p><strong>Dernière Collecte :</strong> {{ $campagne->collectes->sortByDesc('created_at')->first()?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                        @else
                                            <p><strong>Dernière Collecte :</strong> Aucune</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.campagnes.edit', $campagne) }}" class="btn btn-outline-primary">
                                <i class="ti-pencil mr-2"></i>Modifier
                            </a>
                            <form method="POST" action="{{ route('admin.campagnes.destroy', $campagne) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette campagne ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="ti-trash mr-2"></i>Supprimer
                                </button>
                            </form>
                            <a href="{{ route('admin.campagnes.index') }}" class="btn btn-secondary">
                                <i class="ti-arrow-left mr-2"></i>Retour à la Liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
