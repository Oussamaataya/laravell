@extends('layouts.app')

@section('title', 'Organiser un Recyclage - EcoEvent')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('recyclages.index') }}">Recyclages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Organiser un recyclage</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">Organiser un Recyclage</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('recyclages.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre du recyclage <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('titre') is-invalid @enderror" 
                                           id="titre" 
                                           name="titre" 
                                           value="{{ old('titre') }}" 
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
                                                    {{ old('type_recyclage_id') == $type->id ? 'selected' : '' }}>
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
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Décrivez les matériaux acceptés, les conditions de collecte, etc.</div>
                        </div>

                        <div class="mb-3">
                            <label for="lieu" class="form-label">Lieu de collecte <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('lieu') is-invalid @enderror" 
                                   id="lieu" 
                                   name="lieu" 
                                   value="{{ old('lieu') }}" 
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
                                           value="{{ old('date_collecte') }}" 
                                           min="{{ date('Y-m-d') }}"
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
                                           value="{{ old('heure_debut') }}" 
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
                                           value="{{ old('heure_fin') }}" 
                                           required>
                                    @error('heure_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantite_prevue" class="form-label">Quantité prévue (kg)</label>
                                    <input type="number" 
                                           class="form-control @error('quantite_prevue') is-invalid @enderror" 
                                           id="quantite_prevue" 
                                           name="quantite_prevue" 
                                           value="{{ old('quantite_prevue') }}" 
                                           step="0.01" 
                                           min="0">
                                    @error('quantite_prevue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Estimation de la quantité de matériaux à collecter (optionnel)</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes supplémentaires</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Informations complémentaires, consignes particulières, etc.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('recyclages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Organiser le Recyclage
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Conseils</h5>
                </div>
                <div class="card-body">
                    <h6>Pour organiser un bon recyclage :</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Choisissez un lieu accessible</li>
                        <li><i class="fas fa-check text-success me-2"></i>Prévoyez suffisamment de temps</li>
                        <li><i class="fas fa-check text-success me-2"></i>Communiquez clairement les matériaux acceptés</li>
                        <li><i class="fas fa-check text-success me-2"></i>Pensez au matériel de tri</li>
                    </ul>
                    
                    <div class="mt-3">
                        <h6>Types populaires :</h6>
                        @foreach($typeRecyclages->take(3) as $type)
                            <span class="badge bg-secondary me-1 mb-1">{{ $type->nom }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
