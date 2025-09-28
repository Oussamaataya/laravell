@extends('layouts.back')

@section('title', 'Détails de la Collecte - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Détails de la Collecte #{{ $collecte->id }}</h4>
                        <p class="card-description">Informations complètes sur cette collecte.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Informations de la Collecte</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>ID :</strong> {{ $collecte->id }}</p>
                                        <p><strong>Montant :</strong> {{ number_format($collecte->montant, 2) }} €</p>
                                        <p><strong>Méthode de Paiement :</strong> {{ ucfirst($collecte->methode_paiement) }}</p>
                                        <p><strong>Statut :</strong>
                                            <span class="badge badge-{{ $collecte->statut === 'validé' ? 'success' : ($collecte->statut === 'en_attente' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($collecte->statut) }}
                                            </span>
                                        </p>
                                        <p><strong>Créée le :</strong> {{ $collecte->created_at->format('d/m/Y H:i') }}</p>
                                        <p><strong>Mise à jour le :</strong> {{ $collecte->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Liens Associés</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Campagne :</strong> {{ $collecte->campagne->nom ?? 'N/A' }}</p>
                                        <p><strong>Objectif Campagne :</strong> {{ number_format($collecte->campagne->montant_objectif ?? 0, 2) }} €</p>
                                        <p><strong>Montant Actuel Campagne :</strong> {{ number_format($collecte->campagne->montant_actuel ?? 0, 2) }} €</p>
                                        <p><strong>Statut Campagne :</strong> {{ ucfirst($collecte->campagne->statut ?? 'N/A') }}</p>
                                        <hr>
                                        <p><strong>Utilisateur :</strong> {{ $collecte->utilisateur->name ?? 'N/A' }}</p>
                                        <p><strong>Email Utilisateur :</strong> {{ $collecte->utilisateur->email ?? 'N/A' }}</p>
                                        <p><strong>Rôle :</strong> {{ ucfirst($collecte->utilisateur->role ?? 'N/A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.collectes.edit', $collecte) }}" class="btn btn-outline-primary">
                                <i class="ti-pencil mr-2"></i>Modifier
                            </a>
                            <form method="POST" action="{{ route('admin.collectes.destroy', $collecte) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette collecte ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="ti-trash mr-2"></i>Supprimer
                                </button>
                            </form>
                            <a href="{{ route('admin.collectes.index') }}" class="btn btn-secondary">
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
