@extends('layouts.back')

@section('title', 'Gestion des Avis')

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

                    <h4 class="card-title">Tous les Avis</h4>
                    <p class="card-description">
                        Gérez tous les avis des réclamations.
                    </p>
                    <a href="{{ route('admin.avis.create') }}" class="btn btn-primary mb-3">
                        <i class="ti-plus"></i> Nouvel Avis
                    </a>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="avis-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Note</th>
                                    <th>Commentaire</th>
                                    <th>Utilisateur</th>
                                    <th>Réclamation</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($avis as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $item->note)
                                                    <i class="ti-star text-warning"></i>
                                                @else
                                                    <i class="ti-star-outline text-muted"></i>
                                                @endif
                                            @endfor
                                            ({{ $item->note }}/5)
                                        </td>
                                        <td>{{ Str::limit($item->commentaire, 50) }}</td>
                                        <td>{{ $item->user->name ?? 'N/A' }} ({{ $item->user->email ?? '' }})</td>
                                        <td>
                                            @if($item->reclamation)
                                                <a href="{{ route('admin.reclamations.show', $item->reclamation) }}">{{ Str::limit($item->reclamation->sujet, 30) }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.avis.show', $item) }}" class="btn btn-info btn-sm">
                                                    <i class="ti-eye"></i> Voir
                                                </a>
                                                <a href="{{ route('admin.avis.edit', $item) }}" class="btn btn-warning btn-sm">
                                                    <i class="ti-pencil"></i> Éditer
                                                </a>
                                                <form action="{{ route('admin.avis.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?')">
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
                        {{ $avis->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#avis-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "pageLength": 10,
        "order": [[ 0, "desc" ]]
    });
});
</script>
@endsection
