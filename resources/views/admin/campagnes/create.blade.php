@extends('layouts.back')

@section('title', 'Créer une Campagne - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Créer une Nouvelle Campagne</h4>
                        <p class="card-description">Remplissez les informations pour créer une campagne de collecte.</p>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.campagnes.store') }}" method="POST" id="campagneForm">
                            @csrf
                            
                            <div class="form-group">
                                <label for="nom">Nom de la Campagne *</label>
                                <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" 
                                       value="{{ old('nom') }}" placeholder="Ex: Campagne Écologique 2024" >
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="nom_error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4" placeholder="Décrivez les objectifs de la campagne...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="description_error"></div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="montant_objectif">Montant Objectif (€) *</label>
                                        <input type="number" name="montant_objectif" id="montant_objectif" class="form-control @error('montant_objectif') is-invalid @enderror" 
                                               value="{{ old('montant_objectif') }}" step="0.01" min="0.01" >
                                        @error('montant_objectif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="montant_objectif_error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="montant_actuel">Montant Actuel (€)</label>
                                        <input type="number" name="montant_actuel" id="montant_actuel" class="form-control @error('montant_actuel') is-invalid @enderror" 
                                               value="{{ old('montant_actuel', 0) }}" step="0.01" min="0">
                                        @error('montant_actuel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="montant_actuel_error"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_debut">Date de Début *</label>
                                        <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut') is-invalid @enderror" 
                                               value="{{ old('date_debut') }}" >
                                        @error('date_debut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="date_debut_error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_fin">Date de Fin *</label>
                                        <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin') is-invalid @enderror" 
                                               value="{{ old('date_fin') }}" >
                                        @error('date_fin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="date_fin_error"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="statut">Statut *</label>
                                        <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror" >
                                            <option value="">Sélectionnez un statut</option>
                                            <option value="brouillon" {{ old('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                            <option value="active" {{ old('statut') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="terminée" {{ old('statut') == 'terminée' ? 'selected' : '' }}>Terminée</option>
                                            <option value="annulée" {{ old('statut') == 'annulée' ? 'selected' : '' }}>Annulée</option>
                                        </select>
                                        @error('statut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="statut_error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="organisateur_id">Organisateur *</label>
                                        <select name="organisateur_id" id="organisateur_id" class="form-control @error('organisateur_id') is-invalid @enderror" >
                                            <option value="">Sélectionnez un organisateur</option>
                                            @foreach($utilisateurs as $user)
                                                <option value="{{ $user->id }}" {{ old('organisateur_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('organisateur_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="organisateur_id_error"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-check mr-2"></i>Créer la Campagne
                            </button>
                            <a href="{{ route('admin.campagnes.index') }}" class="btn btn-secondary">
                                <i class="ti-arrow-left mr-2"></i>Retour à la Liste
                            </a>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const form = document.getElementById('campagneForm');
                                const fields = {
                                    nom: document.getElementById('nom'),
                                    montant_objectif: document.getElementById('montant_objectif'),
                                    date_debut: document.getElementById('date_debut'),
                                    date_fin: document.getElementById('date_fin'),
                                    statut: document.getElementById('statut'),
                                    organisateur_id: document.getElementById('organisateur_id')
                                };

                                // Real-time validation
                                Object.keys(fields).forEach(key => {
                                    if (fields[key].tagName === 'SELECT') {
                                        fields[key].addEventListener('change', validateField.bind(null, key));
                                        fields[key].addEventListener('blur', validateField.bind(null, key));
                                    } else {
                                        fields[key].addEventListener('input', validateField.bind(null, key));
                                        fields[key].addEventListener('blur', validateField.bind(null, key));
                                    }
                                });

                                // Set min date for date_debut to today
                                const today = new Date().toISOString().split('T')[0];
                                document.getElementById('date_debut').setAttribute('min', today);

                                function validateField(fieldName) {
                                    const field = fields[fieldName];
                                    const errorElement = document.getElementById(fieldName + '_error');
                                    let isValid = true;
                                    let errorMessage = '';

                                    if (fieldName === 'nom') {
                                        if (!field.value.trim()) {
                                            isValid = false;
                                            errorMessage = 'Le nom de la campagne est requis.';
                                        } else if (field.value.length < 3) {
                                            isValid = false;
                                            errorMessage = 'Le nom doit contenir au moins 3 caractères.';
                                        }
                                    } else if (fieldName === 'montant_objectif') {
                                        const value = parseFloat(field.value);
                                        if (!field.value || value < 0.01) {
                                            isValid = false;
                                            errorMessage = 'Le montant objectif doit être supérieur à 0.01 €.';
                                        }
                                    if (fieldName === 'date_debut') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'La date de début est requise.';
                                        } else {
                                            const today = new Date();
                                            today.setHours(0, 0, 0, 0);
                                            const debut = new Date(field.value);
                                            if (debut < today) {
                                                isValid = false;
                                                errorMessage = 'La date de début doit être à partir d\'aujourd\'hui.';
                                            } else {
                                                const fin = new Date(document.getElementById('date_fin').value);
                                                if (fin && debut >= fin) {
                                                    isValid = false;
                                                    errorMessage = 'La date de début doit être antérieure à la date de fin.';
                                                }
                                            }
                                        }
                                    } else if (fieldName === 'date_fin') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'La date de fin est requise.';
                                        } else {
                                            const debut = new Date(document.getElementById('date_debut').value);
                                            const fin = new Date(field.value);
                                            if (debut && fin <= debut) {
                                                isValid = false;
                                                errorMessage = 'La date de fin doit être postérieure à la date de début.';
                                            }
                                        }
                                    } else if (fieldName === 'statut') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'Veuillez sélectionner un statut.';
                                        }
                                    } else if (fieldName === 'organisateur_id') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'Veuillez sélectionner un organisateur.';
                                        }
                                    }

                                    if (isValid) {
                                        field.classList.remove('is-invalid');
                                        field.classList.add('is-valid');
                                        errorElement.textContent = '';
                                        errorElement.style.display = 'none';
                                    } else {
                                        field.classList.remove('is-valid');
                                        field.classList.add('is-invalid');
                                        errorElement.innerHTML = '<i class="fas fa-exclamation-circle text-danger me-1"></i>' + errorMessage;
                                        errorElement.style.display = 'block';
                                        errorElement.style.color = '#dc3545';
                                        errorElement.style.fontSize = '0.875em';
                                    }
                                }

                                // Form submission validation
                                form.addEventListener('submit', function(e) {
                                    let isFormValid = true;

                                    Object.keys(fields).forEach(key => {
                                        validateField(key);
                                        if (fields[key].classList.contains('is-invalid')) {
                                            isFormValid = false;
                                        }
                                    });

                                    if (!isFormValid) {
                                        e.preventDefault();
                                        alert('Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
