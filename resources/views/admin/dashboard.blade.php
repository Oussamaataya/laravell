@extends('layouts.back')

@section('title', 'Dashboard Admin - EcoEvent')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Bienvenue {{ auth()->user()->name }}</h3>
                        <h6 class="font-weight-normal mb-0">Tableau de bord administrateur EcoEvent</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex gap-2">
                            <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Aujourd'hui ({{ now()->format('d M Y') }})
                                </button>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" title="Déconnexion">
                                    <i class="ti-power-off"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">Publications</h4>
                                <h2 class="text-primary font-weight-bold">{{ \App\Models\Publication::count() }}</h2>
                                <p class="text-muted mb-0">Total des publications</p>
                            </div>
                            <div class="align-self-center">
                                <i class="ti-book text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Approuvées</small>
                                <small class="text-success">{{ \App\Models\Publication::where('is_approved', true)->count() }}</small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">En attente</small>
                                <small class="text-warning">{{ \App\Models\Publication::where('is_approved', false)->count() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img src="{{ Vite::asset('resources/assets-back/images/dashboard/people.svg') }}" alt="people">
                        <div class="weather-info">
                            <div class="d-flex">
                                <div>
                                    <h2 class="mb-0 font-weight-normal"><i class="icon-sun mr-2"></i>{{ \App\Models\User::count() }}</h2>
                                </div>
                                <div class="ml-2">
                                    <h4 class="location font-weight-normal">Utilisateurs</h4>
                                    <h6 class="font-weight-normal">Total inscrits</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Statistiques utilisateurs</p>
                        <p class="font-weight-500">Répartition des rôles</p>
                        <div class="d-flex justify-content-between pb-2">
                            <div class="d-flex">
                                <i class="mdi mdi-account-box text-info mr-1"></i>
                                <p class="text-muted mb-0">Administrateurs</p>
                            </div>
                            <div class="d-flex">
                                <p class="mb-0">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <div class="d-flex">
                                <i class="mdi mdi-account text-success mr-1"></i>
                                <p class="text-muted mb-0">Utilisateurs</p>
                            </div>
                            <div class="d-flex">
                                <p class="mb-0">{{ \App\Models\User::where('role', 'user')->count() }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <i class="mdi mdi-account-check text-primary mr-1"></i>
                                <p class="text-muted mb-0">Actifs</p>
                            </div>
                            <div class="d-flex">
                                <p class="mb-0">{{ \App\Models\User::where('is_active', true)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title mb-0">Actions rapides</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Lien</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Gestion des utilisateurs</td>
                                        <td>Voir, créer, modifier et supprimer des utilisateurs</td>
                                        <td><a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">Accéder</a></td>
                                    </tr>
                                    <tr>
                                        <td>Créer un utilisateur</td>
                                        <td>Ajouter un nouveau compte utilisateur</td>
                                        <td><a href="{{ route('admin.users.create') }}" class="btn btn-outline-success btn-sm">Créer</a></td>
                                    </tr>
                                    <tr>
                                        <td>Gestion des publications</td>
                                        <td>Voir et gérer toutes les publications</td>
                                        <td><a href="{{ route('publications.index') }}" class="btn btn-outline-primary btn-sm">Accéder</a></td>
                                    </tr>
                                    <tr>
                                        <td>Créer une publication</td>
                                        <td>Ajouter une nouvelle publication</td>
                                        <td><a href="{{ route('publications.create') }}" class="btn btn-outline-success btn-sm">Créer</a></td>
                                    </tr>
                                    <tr>
                                        <td>Mon profil</td>
                                        <td>Modifier mes informations personnelles</td>
                                        <td><a href="{{ route('profile.edit') }}" class="btn btn-outline-info btn-sm">Modifier</a></td>
                                    </tr>
                                </tbody>
                            </table>
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
