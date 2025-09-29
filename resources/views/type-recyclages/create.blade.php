@extends('layouts.back')

@section('title', 'Créer un Type de Recyclage')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Créer un Type de Recyclage</h3>
                        <h6 class="font-weight-normal mb-0">Ajouter un nouveau type de matériau recyclable</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <a href="{{ route('admin.type-recyclages.index') }}" class="btn btn-secondary btn-sm">
                                <i class="ti-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Informations du Type</h4>
                        
                        <form action="{{ route('admin.type-recyclages.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="nom">Nom du Type <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom') }}" 
                                       required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="couleur">Couleur (Hex)</label>
                                        <input type="color" 
                                               class="form-control @error('couleur') is-invalid @enderror" 
                                               id="couleur" 
                                               name="couleur" 
                                               value="{{ old('couleur', '#28a745') }}">
                                        @error('couleur')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Couleur pour l'affichage du type</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="icone">Icône (Classe CSS)</label>
                                        <input type="text" 
                                               class="form-control @error('icone') is-invalid @enderror" 
                                               id="icone" 
                                               name="icone" 
                                               value="{{ old('icone') }}" 
                                               placeholder="Ex: ti-package, fas fa-recycle">
                                        @error('icone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Classe CSS de l'icône (optionnel)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="actif" 
                                           name="actif" 
                                           value="1" 
                                           {{ old('actif', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="actif">
                                        Type actif
                                    </label>
                                </div>
                                <small class="form-text text-muted">Les types inactifs ne seront pas disponibles lors de la création de recyclages</small>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="ti-save"></i> Créer le Type
                                </button>
                                <a href="{{ route('admin.type-recyclages.index') }}" class="btn btn-light">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Aide</h4>
                        <div class="mt-3">
                            <h6>Exemples de types :</h6>
                            <ul class="list-unstyled">
                                <li><i class="ti-package text-success"></i> Plastique</li>
                                <li><i class="ti-files text-warning"></i> Papier</li>
                                <li><i class="ti-cup text-info"></i> Verre</li>
                                <li><i class="ti-settings text-secondary"></i> Métal</li>
                                <li><i class="ti-mobile text-danger"></i> Électronique</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Icônes suggérées :</h6>
                            <p class="text-muted small">
                                Utilisez les classes Themify Icons (ti-*) ou Font Awesome (fas fa-*) pour les icônes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
