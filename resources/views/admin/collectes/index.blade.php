@extends('layouts.back')

@section('title', 'Gestion des Collectes - Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Gestion des Collectes</h4>
                        <p class="card-description">Liste des collectes effectuées dans les campagnes.</p>
                        
                        @if($collectes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Montant</th>
                                            <th>Méthode de Paiement</th>
                                            <th>Statut</th>
                                            <th>Campagne</th>
                                            <th>Utilisateur</th>
                                            <th>Créé le</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collectes as $collecte)
                                            <tr>
                                                <td>{{ $collecte->id }}</td>
                                                <td>{{ number_format($collecte->montant, 2) }} €</td>
                                                <td>{{ ucfirst($collecte->methode_paiement) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $collecte->statut === 'validé' ? 'success' : ($collecte->statut === 'en_attente' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($collecte->statut) }}
                                                    </span>
                                                </td>
                                                <td>{{ $collecte->campagne->nom ?? 'N/A' }}</td>
                                                <td>{{ $collecte->utilisateur->name ?? 'N/A' }}</td>
                                                <td>{{ $collecte->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.collectes.show', $collecte) }}" class="btn btn-outline-primary btn-sm">Voir</a>
                                                    <a href="{{ route('admin.collectes.edit', $collecte) }}" class="btn btn-outline-secondary btn-sm">Modifier</a>
                                                    <form method="POST" action="{{ route('admin.collectes.destroy', $collecte) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette collecte ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $collectes->links() }}
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="ti-info-alt mr-2"></i>
                                Aucune collecte pour l'instant. <a href="#" class="alert-link">Ajouter la première collecte</a>.
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
                            <a href="{{ route('admin.collectes.create') }}" class="btn btn-primary">
                                <i class="ti-plus mr-2"></i>Ajouter une Collecte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
