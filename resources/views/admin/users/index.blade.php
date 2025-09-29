@extends('layouts.back')

@section('title', 'Gestion des Utilisateurs - EcoEvent')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Gestion des Utilisateurs</h3>
                        <h6 class="font-weight-normal mb-0">Gérer tous les comptes utilisateurs de la plateforme</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="ti-plus"></i> Créer un utilisateur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nom ou email...">
                            </div>
                            <div class="col-md-2">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="">Tous les rôles</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Utilisateur</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-primary" title="Filtrer">
                                        <i class="mdi mdi-magnify"></i> Filtrer
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Reset">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Table des utilisateurs -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title mb-0">Liste des utilisateurs ({{ $users->total() }} au total)</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th>Dernière connexion</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ Vite::asset('resources/assets-back/images/faces/face1.jpg') }}" 
                                                         alt="user" class="me-2 rounded-circle" width="30" height="30">
                                                    <span class="font-weight-bold">{{ $user->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <label class="badge {{ $user->role === 'admin' ? 'badge-danger' : 'badge-success' }}">
                                                    {{ ucfirst($user->role) }}
                                                </label>
                                            </td>
                                            <td>
                                                <label class="badge {{ $user->is_active ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                                </label>
                                            </td>
                                            <td>
                                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="ti-pencil"></i>
                                                    </a>
                                                    
                                                    @if($user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} btn-sm"
                                                                    title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                                                <i class="ti-{{ $user->is_active ? 'na' : 'check' }}"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                                              class="d-inline" 
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted">Aucun utilisateur trouvé</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif
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
