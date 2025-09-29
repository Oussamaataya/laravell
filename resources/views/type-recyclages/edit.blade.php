@extends('layouts.back')

@section('title', 'Modifier le Type de Recyclage')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Modifier le Type de Recyclage</h3>
                        <h6 class="font-weight-normal mb-0">Modifier les informations du type "{{ $typeRecyclage->nom }}"</h6>
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
                        
                        <form action="{{ route('admin.type-recyclages.update', $typeRecyclage) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nom">Nom du Type <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom', $typeRecyclage->nom) }}" 
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
                                          rows="4">{{ old('description', $typeRecyclage->description) }}</textarea>
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
                                               value="{{ old('couleur', $typeRecyclage->couleur ?? '#28a745') }}">
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
                                               value="{{ old('icone', $typeRecyclage->icone) }}" 
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
                                           {{ old('actif', $typeRecyclage->actif) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="actif">
                                        Type actif
                                    </label>
                                </div>
                                <small class="form-text text-muted">Les types inactifs ne seront pas disponibles lors de la création de recyclages</small>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="ti-save"></i> Mettre à jour
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
                        <h4 class="card-title">Aperçu</h4>
                        <div class="mt-3">
                            <div class="d-flex align-items-center mb-3">
                                @if($typeRecyclage->icone)
                                    <i class="{{ $typeRecyclage->icone }} mr-2" style="color: {{ $typeRecyclage->couleur ?? '#28a745' }}; font-size: 1.5rem;"></i>
                                @endif
                                <span class="badge" style="background-color: {{ $typeRecyclage->couleur ?? '#28a745' }}; color: white;">
                                    {{ $typeRecyclage->nom }}
                                </span>
                            </div>
                            <p class="text-muted small">{{ $typeRecyclage->description }}</p>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Statistiques :</h6>
                            <p class="text-muted small">
                                <strong>{{ $typeRecyclage->recyclages()->count() }}</strong> recyclages utilisent ce type
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
