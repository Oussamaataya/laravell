@extends('layouts.back')

@section('title', 'Modifier l\'Utilisateur - EcoEvent')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Modifier l'Utilisateur</h3>
                        <h6 class="font-weight-normal mb-0">Modifier les informations de {{ $user->name }}</h6>
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
                        
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="forms-sample">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="name">Nom complet *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" 
                                       placeholder="Nom complet" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Adresse email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       placeholder="Email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Nouveau mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Laisser vide pour conserver le mot de passe actuel">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Laisser vide si vous ne souhaitez pas changer le mot de passe
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirmer le nouveau mot de passe">
                            </div>
                            
                            <div class="form-group">
                                <label for="role">Rôle *</label>
                                <select class="form-control @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                        Utilisateur
                                    </option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
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
                                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        Compte actif
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Un compte inactif ne peut pas se connecter à la plateforme
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti-save"></i> Mettre à jour
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
                        <h4 class="card-title">Informations du compte</h4>
                        
                        <div class="profile-card">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ Vite::asset('resources/assets-back/images/faces/face1.jpg') }}" 
                                     alt="user" class="me-3 rounded-circle" width="50" height="50">
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="mb-0">{{ $user->role === 'admin' ? 'Admin' : 'User' }}</h4>
                                        <small class="text-muted">Rôle</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="mb-0">{{ $user->is_active ? 'Actif' : 'Inactif' }}</h4>
                                    <small class="text-muted">Statut</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Compte créé</h6>
                                    <p class="timeline-text">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($user->last_login_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Dernière connexion</h6>
                                        <p class="timeline-text">{{ $user->last_login_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Dernière modification</h6>
                                    <p class="timeline-text">{{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
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

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 17px;
    bottom: -20px;
    width: 2px;
    background: #e9ecef;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush
@endsection
