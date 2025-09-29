@extends('layouts.back')

@section('title', 'Détails de la Réclamation')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Réclamation #{{ $reclamation->id }}</h4>
                    <p class="card-description">
                        Détails de la réclamation.
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informations Principales</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Sujet:</strong> {{ $reclamation->sujet }}</p>
                                    <p><strong>Description:</strong></p>
                                    <p class="text-muted">{{ $reclamation->description }}</p>
                                    <p><strong>Statut:</strong>
                                        @if($reclamation->statut === 'pending')
                                            <span class="badge badge-warning">En attente</span>
                                        @elseif($reclamation->statut === 'resolved')
                                            <span class="badge badge-success">Résolue</span>
                                        @endif
                                    </p>
                                    <p><strong>Créée le:</strong> {{ $reclamation->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Utilisateur</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nom:</strong> {{ $reclamation->user->name ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $reclamation->user->email ?? 'N/A' }}</p>
                                    @if($reclamation->user->phone ?? false)
                                        <p><strong>Téléphone:</strong> {{ $reclamation->user->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Avis Associés ({{ $reclamation->avis->count() }})</h5>
                                    <a href="{{ route('admin.reclamations.avis.create', $reclamation) }}" class="btn btn-primary btn-sm">
                                        <i class="ti-plus"></i> Ajouter un Avis
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($reclamation->avis->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Note</th>
                                                        <th>Commentaire</th>
                                                        <th>Utilisateur</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reclamation->avis as $avis)
                                                        <tr>
                                                            <td>{{ $avis->id }}</td>
                                                            <td>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $avis->note)
                                                                        <i class="ti-star text-warning"></i>
                                                                    @else
                                                                        <i class="ti-star-outline text-muted"></i>
                                                                    @endif
                                                                @endfor
                                                                ({{ $avis->note }}/5)
                                                            </td>
                                                            <td>{{ Str::limit($avis->commentaire, 50) }}</td>
                                                            <td>{{ $avis->user->name ?? 'N/A' }}</td>
                                                            <td>{{ $avis->created_at->format('d/m/Y') }}</td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <a href="{{ route('admin.reclamations.avis.show', [$reclamation, $avis]) }}" class="btn btn-info btn-sm">
                                                                        <i class="ti-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.reclamations.avis.edit', [$reclamation, $avis]) }}" class="btn btn-warning btn-sm">
                                                                        <i class="ti-pencil"></i>
                                                                    </a>
                                                                    <form action="{{ route('admin.reclamations.avis.destroy', [$reclamation, $avis]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                                            <i class="ti-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucun avis associé à cette réclamation.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <a href="{{ route('admin.reclamations.edit', $reclamation) }}" class="btn btn-warning">
                            <i class="ti-pencil"></i> Modifier
                        </a>
                        <a href="{{ route('admin.reclamations.index') }}" class="btn btn-secondary">
                            <i class="ti-arrow-left"></i> Retour à la liste
                        </a>
                        <form action="{{ route('admin.reclamations.destroy', $reclamation) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation et ses avis associés ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
