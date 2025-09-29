@extends('layouts.back')

@section('title', 'Ajouter une Collecte - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Ajouter une Nouvelle Collecte</h4>
                        <p class="card-description">Remplissez le formulaire pour créer une nouvelle collecte.</p>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.collectes.store') }}" method="POST" id="collecteForm">
                            @csrf
                            
                            <div class="form-group">
                                <label for="campagne_id">Campagne *</label>
                                <select name="campagne_id" id="campagne_id" class="form-control @error('campagne_id') is-invalid @enderror" >
                                    <option value="">Sélectionnez une campagne</option>
                                    @foreach($campagnes as $campagne)
                                        <option value="{{ $campagne->id }}" {{ old('campagne_id') == $campagne->id ? 'selected' : '' }}>
                                            {{ $campagne->nom }} ({{ $campagne->montant_actuel }} / {{ $campagne->montant_objectif }} €)
                                        </option>
                                    @endforeach
                                </select>
                                @error('campagne_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="campagne_id_error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="utilisateur_id">Utilisateur *</label>
                                <select name="utilisateur_id" id="utilisateur_id" class="form-control @error('utilisateur_id') is-invalid @enderror" >
                                    <option value="">Sélectionnez un utilisateur</option>
                                    @foreach($utilisateurs as $user)
                                        <option value="{{ $user->id }}" {{ old('utilisateur_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('utilisateur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="utilisateur_id_error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="montant">Montant (€) *</label>
                                <input type="number" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" 
                                       value="{{ old('montant') }}" step="0.01" min="0.01" >
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="montant_error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="methode_paiement">Méthode de Paiement *</label>
                                <select name="methode_paiement" id="methode_paiement" class="form-control @error('methode_paiement') is-invalid @enderror" >
                                    <option value="">Sélectionnez une méthode</option>
                                    <option value="carte" {{ old('methode_paiement') == 'carte' ? 'selected' : '' }}>Carte Bancaire</option>
                                    <option value="paypal" {{ old('methode_paiement') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="virement" {{ old('methode_paiement') == 'virement' ? 'selected' : '' }}>Virement Bancaire</option>
                                </select>
                                @error('methode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="methode_paiement_error"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="statut">Statut *</label>
                                <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror" >
                                    <option value="">Sélectionnez un statut</option>
                                    <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                                    <option value="validé" {{ old('statut') == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="échoué" {{ old('statut') == 'échoué' ? 'selected' : '' }}>Échoué</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="statut_error"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-check mr-2"></i>Créer la Collecte
                            </button>
                            <a href="{{ route('admin.collectes.index') }}" class="btn btn-secondary">
                                <i class="ti-arrow-left mr-2"></i>Retour à la Liste
                            </a>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const form = document.getElementById('collecteForm');
                                const fields = {
                                    campagne_id: document.getElementById('campagne_id'),
                                    utilisateur_id: document.getElementById('utilisateur_id'),
                                    montant: document.getElementById('montant'),
                                    methode_paiement: document.getElementById('methode_paiement'),
                                    statut: document.getElementById('statut')
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

                                function validateField(fieldName) {
                                    const field = fields[fieldName];
                                    const errorElement = document.getElementById(fieldName + '_error');
                                    let isValid = true;
                                    let errorMessage = '';

                                    if (fieldName === 'campagne_id') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'Veuillez sélectionner une campagne.';
                                        }
                                    } else if (fieldName === 'utilisateur_id') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'Veuillez sélectionner un utilisateur.';
                                        }
                                    } else if (fieldName === 'montant') {
                                        const value = parseFloat(field.value);
                                        if (!field.value || value < 0.01) {
                                            isValid = false;
                                            errorMessage = 'Le montant doit être supérieur à 0.01 €.';
                                        }
                                    } else if (fieldName === 'methode_paiement') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'Veuillez sélectionner une méthode de paiement.';
                                        }
                                    } else if (fieldName === 'statut') {
                                        if (!field.value) {
                                            isValid = false;
                                            errorMessage = 'Veuillez sélectionner un statut.';
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
                                        alert('Veuillez remplir les erreurs dans le formulaire avant de soumettre.');
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
