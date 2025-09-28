@extends('layouts.back')

@section('title', 'Modifier la Campagne - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Modifier la Campagne #{{ $campagne->id }}</h4>
                        <p class="card-description">Modifiez les informations de cette campagne.</p>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.campagnes.update', $campagne) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nom">Nom de la Campagne *</label>
                                <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" 
                                       value="{{ old('nom', $campagne->nom) }}" placeholder="Ex: Campagne Écologique 2024" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4" placeholder="Décrivez les objectifs de la campagne...">{{ old('description', $campagne->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="montant_objectif">Montant Objectif (€) *</label>
                                        <input type="number" name="montant_objectif" id="montant_objectif" class="form-control @error('montant_objectif') is-invalid @enderror" 
                                               value="{{ old('montant_objectif', $campagne->montant_objectif) }}" step="0.01" min="0.01" required>
                                        @error('montant_objectif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="montant_actuel">Montant Actuel (€)</label>
                                        <input type="number" name="montant_actuel" id="montant_actuel" class="form-control @error('montant_actuel') is-invalid @enderror" 
                                               value="{{ old('montant_actuel', $campagne->montant_actuel) }}" step="0.01" min="0">
                                        @error('montant_actuel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_debut">Date de Début *</label>
                                        <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut') is-invalid @enderror" 
                                               value="{{ old('date_debut', $campagne->date_debut?->format('Y-m-d')) }}" required>
                                        @error('date_debut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_fin">Date de Fin *</label>
                                        <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin') is-invalid @enderror" 
                                               value="{{ old('date_fin', $campagne->date_fin?->format('Y-m-d')) }}" required>
                                        @error('date_fin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="statut">Statut *</label>
                                        <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror" required>
                                            <option value="">Sélectionnez un statut</option>
                                            <option value="brouillon" {{ old('statut', $campagne->statut) == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                            <option value="active" {{ old('statut', $campagne->statut) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="terminée" {{ old('statut', $campagne->statut) == 'terminée' ? 'selected' : '' }}>Terminée</option>
                                            <option value="annulée" {{ old('statut', $campagne->statut) == 'annulée' ? 'selected' : '' }}>Annulée</option>
                                        </select>
                                        @error('statut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="organisateur_id">Organisateur *</label>
                                        <select name="organisateur_id" id="organisateur_id" class="form-control @error('organisateur_id') is-invalid @enderror" required>
                                            <option value="">Sélectionnez un organisateur</option>
                                            @foreach($utilisateurs as $user)
                                                <option value="{{ $user->id }}" {{ old('organisateur_id', $campagne->organisateur_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('organisateur_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-check mr-2"></i>Mettre à Jour
                            </button>
                            <a href="{{ route('admin.campagnes.show', $campagne) }}" class="btn btn-secondary">
                                <i class="ti-arrow-left mr-2"></i>Retour aux Détails
                            </a>
                            <a href="{{ route('admin.campagnes.index') }}" class="btn btn-outline-secondary">
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
