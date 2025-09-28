@extends('layouts.back')

@section('title', 'Modifier la Collecte - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Modifier la Collecte #{{ $collecte->id }}</h4>
                        <p class="card-description">Modifiez les informations de cette collecte.</p>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.collectes.update', $collecte) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="campagne_id">Campagne *</label>
                                <select name="campagne_id" id="campagne_id" class="form-control @error('campagne_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez une campagne</option>
                                    @foreach($campagnes as $campagne)
                                        <option value="{{ $campagne->id }}" {{ old('campagne_id', $collecte->campagne_id) == $campagne->id ? 'selected' : '' }}>
                                            {{ $campagne->nom }} ({{ $campagne->montant_actuel }} / {{ $campagne->montant_objectif }} €)
                                        </option>
                                    @endforeach
                                </select>
                                @error('campagne_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="utilisateur_id">Utilisateur *</label>
                                <select name="utilisateur_id" id="utilisateur_id" class="form-control @error('utilisateur_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un utilisateur</option>
                                    @foreach($utilisateurs as $user)
                                        <option value="{{ $user->id }}" {{ old('utilisateur_id', $collecte->utilisateur_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('utilisateur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="montant">Montant (€) *</label>
                                <input type="number" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" 
                                       value="{{ old('montant', $collecte->montant) }}" step="0.01" min="0.01" required>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="methode_paiement">Méthode de Paiement *</label>
                                <select name="methode_paiement" id="methode_paiement" class="form-control @error('methode_paiement') is-invalid @enderror" required>
                                    <option value="">Sélectionnez une méthode</option>
                                    <option value="carte" {{ old('methode_paiement', $collecte->methode_paiement) == 'carte' ? 'selected' : '' }}>Carte Bancaire</option>
                                    <option value="paypal" {{ old('methode_paiement', $collecte->methode_paiement) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="virement" {{ old('methode_paiement', $collecte->methode_paiement) == 'virement' ? 'selected' : '' }}>Virement Bancaire</option>
                                </select>
                                @error('methode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="statut">Statut *</label>
                                <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un statut</option>
                                    <option value="en_attente" {{ old('statut', $collecte->statut) == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                                    <option value="validé" {{ old('statut', $collecte->statut) == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="échoué" {{ old('statut', $collecte->statut) == 'échoué' ? 'selected' : '' }}>Échoué</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-check mr-2"></i>Mettre à Jour
                            </button>
                            <a href="{{ route('admin.collectes.show', $collecte) }}" class="btn btn-secondary">
                                <i class="ti-arrow-left mr-2"></i>Retour aux Détails
                            </a>
                            <a href="{{ route('admin.collectes.index') }}" class="btn btn-outline-secondary">
                                <i class="ti-list mr-2"></i>Retour à la Liste
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
