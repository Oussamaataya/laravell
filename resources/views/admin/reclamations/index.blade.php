@extends('layouts.back')

@section('title', 'Gestion des Réclamations')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <h4 class="card-title">Liste des Réclamations</h4>
                    <p class="card-description">
                        Gérez les réclamations des utilisateurs.
                    </p>
                    <a href="{{ route('admin.reclamations.create') }}" class="btn btn-primary mb-3">
                        <i class="ti-plus"></i> Nouvelle Réclamation
                    </a>
                    <div class="table-responsive">
                       <table class="table table-striped table-hover" id="reclamations-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sujet</th>
                                <th>Description</th>
                                <th>Utilisateur</th>
                                <th>Statut</th>
                                <th>Priorité</th>  <!-- Nouvelle colonne -->
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
@foreach($reclamations as $reclamation)
    @php
        // Déterminer la couleur du badge selon le sentiment
        $sentimentColor = match($reclamation->sentiment) {
            'positif' => 'success',
            'négatif' => 'primary',
            'neutre', null => 'secondary',
            default => 'secondary',
        };
    @endphp
    <tr>
        <td>{{ $reclamation->id }}</td>
        <td>{{ $reclamation->sujet }}</td>
        <td>{{ Str::limit($reclamation->description, 50) }}</td>
        <td>{{ $reclamation->user->name ?? 'N/A' }} ({{ $reclamation->user->email ?? '' }})</td>
        <td>
            @if($reclamation->statut === 'pending')
                <span class="badge badge-warning">En attente</span>
            @elseif($reclamation->statut === 'resolved')
                <span class="badge badge-success">Résolue</span>
            @endif
        </td>
        <td>
            <span class="badge badge-{{ $sentimentColor }}">
                {{ $reclamation->sentiment ?? 'neutre' }}
            </span>
        </td>
      
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.reclamations.show', $reclamation) }}" class="btn btn-info btn-sm">
                    <i class="ti-eye"></i> Voir
                </a>
                <a href="{{ route('admin.reclamations.edit', $reclamation) }}" class="btn btn-warning btn-sm">
                    <i class="ti-pencil"></i> Éditer
                </a>
                <form action="{{ route('admin.reclamations.destroy', $reclamation) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="ti-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </td>
    </tr>
@endforeach
</tbody>

                    </table>

                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $reclamations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#reclamations-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 10,
        "order": [[ 0, "desc" ]]
    });
});
</script>
@endsection
