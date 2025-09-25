@extends('layouts.back')

@section('title', 'Créer un Utilisateur - EcoEvent')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Créer un Utilisateur</h3>
                        <h6 class="font-weight-normal mb-0">Ajouter un nouveau compte utilisateur</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="ti-arrow-left"></i> Retour à la liste
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
                        <h4 class="card-title">Informations de l'utilisateur</h4>
                        
                        <form method="POST" action="{{ route('admin.users.store') }}" class="forms-sample">
                            @csrf
                            
                            <div class="form-group">
                                <label for="name">Nom complet *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Nom complet" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Adresse email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="Email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Mot de passe *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Mot de passe (min. 8 caractères)" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmation">Confirmer le mot de passe *</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirmer le mot de passe" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="role">Rôle *</label>
                                <select class="form-control @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                        Utilisateur
                                    </option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        Administrateur
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" 
                                               name="is_active" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        Compte actif
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Un compte inactif ne peut pas se connecter à la plateforme
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti-save"></i> Créer l'utilisateur
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light">
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
                        <div class="alert alert-info">
                            <h6><i class="ti-info-alt"></i> Informations importantes</h6>
                            <ul class="mb-0">
                                <li>Le mot de passe doit contenir au moins 8 caractères</li>
                                <li>L'email doit être unique dans le système</li>
                                <li>Les administrateurs ont accès à toutes les fonctionnalités</li>
                                <li>Les utilisateurs ont un accès limité</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="ti-alert"></i> Sécurité</h6>
                            <p class="mb-0">
                                Assurez-vous de communiquer le mot de passe de manière sécurisée 
                                à l'utilisateur et demandez-lui de le changer lors de sa première connexion.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                    Copyright © {{ date('Y') }} EcoEvent. Tous droits réservés.
                </span>
                <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
                    Fait avec <i class="ti-heart text-danger ml-1"></i> pour l'environnement
                </span>
            </div>
        </footer>
    </div>
    <!-- main-panel ends -->
</div>
@endsection
