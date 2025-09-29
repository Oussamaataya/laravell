@extends('layouts.app')

@section('title', 'Modifier le Recyclage - EcoEvent')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('recyclages.index') }}">Recyclages</a></li>
            <li class="breadcrumb-item"><a href="{{ route('recyclages.show', $recyclage) }}">{{ $recyclage->titre }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h1 class="h4 mb-0">Modifier le Recyclage</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('recyclages.update', $recyclage) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre du recyclage <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('titre') is-invalid @enderror" 
                                           id="titre" 
                                           name="titre" 
                                           value="{{ old('titre', $recyclage->titre) }}" 
                                           required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type_recyclage_id" class="form-label">Type de recyclage <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type_recyclage_id') is-invalid @enderror" 
                                            id="type_recyclage_id" 
                                            name="type_recyclage_id" 
                                            required>
                                        <option value="">Choisir un type</option>
                                        @foreach($typeRecyclages as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('type_recyclage_id', $recyclage->type_recyclage_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_recyclage_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      required>{{ old('description', $recyclage->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lieu" class="form-label">Lieu de collecte <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('lieu') is-invalid @enderror" 
                                   id="lieu" 
                                   name="lieu" 
                                   value="{{ old('lieu', $recyclage->lieu) }}" 
                                   required>
                            @error('lieu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_collecte" class="form-label">Date de collecte <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('date_collecte') is-invalid @enderror" 
                                           id="date_collecte" 
                                           name="date_collecte" 
                                           value="{{ old('date_collecte', $recyclage->date_collecte->format('Y-m-d')) }}" 
                                           required>
                                    @error('date_collecte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="heure_debut" class="form-label">Heure de début <span class="text-danger">*</span></label>
                                    <input type="time" 
                                           class="form-control @error('heure_debut') is-invalid @enderror" 
                                           id="heure_debut" 
                                           name="heure_debut" 
                                           value="{{ old('heure_debut', $recyclage->heure_debut->format('H:i')) }}" 
                                           required>
                                    @error('heure_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="heure_fin" class="form-label">Heure de fin <span class="text-danger">*</span></label>
                                    <input type="time" 
                                           class="form-control @error('heure_fin') is-invalid @enderror" 
                                           id="heure_fin" 
                                           name="heure_fin" 
                                           value="{{ old('heure_fin', $recyclage->heure_fin->format('H:i')) }}" 
                                           required>
                                    @error('heure_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantite_prevue" class="form-label">Quantité prévue (kg)</label>
                                    <input type="number" 
                                           class="form-control @error('quantite_prevue') is-invalid @enderror" 
                                           id="quantite_prevue" 
                                           name="quantite_prevue" 
                                           value="{{ old('quantite_prevue', $recyclage->quantite_prevue) }}" 
                                           step="0.01" 
                                           min="0">
                                    @error('quantite_prevue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantite_collectee" class="form-label">Quantité collectée (kg)</label>
                                    <input type="number" 
                                           class="form-control @error('quantite_collectee') is-invalid @enderror" 
                                           id="quantite_collectee" 
                                           name="quantite_collectee" 
                                           value="{{ old('quantite_collectee', $recyclage->quantite_collectee) }}" 
                                           step="0.01" 
                                           min="0">
                                    @error('quantite_collectee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-control @error('statut') is-invalid @enderror" 
                                            id="statut" 
                                            name="statut" 
                                            required>
                                        <option value="planifie" {{ old('statut', $recyclage->statut) == 'planifie' ? 'selected' : '' }}>Planifié</option>
                                        <option value="en_cours" {{ old('statut', $recyclage->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                        <option value="termine" {{ old('statut', $recyclage->statut) == 'termine' ? 'selected' : '' }}>Terminé</option>
                                        <option value="annule" {{ old('statut', $recyclage->statut) == 'annule' ? 'selected' : '' }}>Annulé</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes supplémentaires</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes', $recyclage->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('recyclages.show', $recyclage) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Organisateur :</small><br>
                        <strong>{{ $recyclage->user->name }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Créé le :</small><br>
                        {{ $recyclage->created_at->format('d/m/Y à H:i') }}
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Dernière modification :</small><br>
                        {{ $recyclage->updated_at->format('d/m/Y à H:i') }}
                    </div>

                    @if($recyclage->quantite_prevue && $recyclage->quantite_collectee)
                        <div class="mb-3">
                            <small class="text-muted">Progression :</small><br>
                            @php
                                $pourcentage = ($recyclage->quantite_collectee / $recyclage->quantite_prevue) * 100;
                            @endphp
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ min($pourcentage, 100) }}%" aria-valuenow="{{ $pourcentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($pourcentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
