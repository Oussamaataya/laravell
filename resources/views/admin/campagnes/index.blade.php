@extends('layouts.back')

@section('title', 'Gestion des Campagnes - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Gestion des Campagnes</h4>
                        <p class="card-description">Liste des campagnes de collecte de fonds.</p>
                        
                        @if($campagnes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Objectif (€)</th>
                                            <th>Actuel (€)</th>
                                            <th>Statut</th>
                                            <th>Organisateur</th>
                                            <th>Date Début</th>
                                            <th>Date Fin</th>
                                            <th>Collectes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($campagnes as $campagne)
                                            <tr>
                                                <td>{{ $campagne->id }}</td>
                                                <td>{{ Str::limit($campagne->nom, 30) }}</td>
                                                <td>{{ Str::limit($campagne->description, 50) }}</td>
                                                <td>{{ number_format($campagne->montant_objectif, 2) }}</td>
                                                <td>{{ number_format($campagne->montant_actuel, 2) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $campagne->statut === 'active' ? 'success' : ($campagne->statut === 'brouillon' ? 'warning' : ($campagne->statut === 'terminée' ? 'info' : 'danger')) }}">
                                                        {{ ucfirst($campagne->statut) }}
                                                    </span>
                                                </td>
                                                <td>{{ $campagne->organisateur->name ?? 'N/A' }}</td>
                                                <td>{{ $campagne->date_debut?->format('d/m/Y') ?? 'N/A' }}</td>
                                                <td>{{ $campagne->date_fin?->format('d/m/Y') ?? 'N/A' }}</td>
                                                <td>{{ $campagne->collectes_count }}</td>
                                                <td style="white-space: nowrap;">
                                                    <a href="{{ route('admin.campagnes.show', $campagne) }}" class="btn btn-sm btn-outline-primary me-1" title="Voir"><i class="ti-eye"></i></a>
                                                    <a href="{{ route('admin.campagnes.edit', $campagne) }}" class="btn btn-sm btn-outline-secondary me-1" title="Modifier"><i class="ti-pencil"></i></a>
                                                    <form method="POST" action="{{ route('admin.campagnes.destroy', $campagne) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette campagne ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="ti-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $campagnes->links() }}
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="ti-info-alt mr-2"></i>
                                Aucune campagne pour l'instant. <a href="{{ route('admin.campagnes.create') }}" class="alert-link">Créer la première campagne</a>.
                            </div>
                        @endif
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('admin.campagnes.create') }}" class="btn btn-primary">
                                <i class="ti-plus mr-2"></i>Ajouter une Campagne
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
