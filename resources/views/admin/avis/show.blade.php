@extends('layouts.back')

@section('title', 'Détails de l\'Avis')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Avis #{{ $avis->id }}</h4>
                    <p class="card-description">
                        Détails de l'avis.
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informations de l'Avis</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Note:</strong>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $avis->note)
                                                <i class="ti-star text-warning"></i>
                                            @else
                                                <i class="ti-star-outline text-muted"></i>
                                            @endif
                                        @endfor
                                        ({{ $avis->note }}/5)
                                    </p>
                                    <p><strong>Commentaire:</strong></p>
                                    <p class="text-muted">{{ $avis->commentaire }}</p>
                                    <p><strong>Créé le:</strong> {{ $avis->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Utilisateur</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nom:</strong> {{ $avis->user->name ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $avis->user->email ?? 'N/A' }}</p>
                                    @if($avis->user->phone ?? false)
                                        <p><strong>Téléphone:</strong> {{ $avis->user->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($reclamation)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Réclamation Associée</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Sujet:</strong> {{ $reclamation->sujet }}</p>
                                    <p><strong>Description:</strong> {{ Str::limit($reclamation->description, 100) }}</p>
                                    <p><strong>Statut:</strong>
                                        @if($reclamation->statut === 'en_attente')
                                            <span class="badge badge-warning">En attente</span>
                                        @elseif($reclamation->statut === 'en_cours')
                                            <span class="badge badge-info">En cours</span>
                                        @elseif($reclamation->statut === 'traitee')
                                            <span class="badge badge-success">Traité</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group mt-4">
                        <a href="{{ route('admin.avis.edit', $avis->id) }}" class="btn btn-warning">
                            <i class="ti-pencil"></i> Modifier
                        </a>
                        <a href="{{ route('admin.avis.index') }}" class="btn btn-secondary">
                            <i class="ti-arrow-left"></i> Retour aux Avis
                        </a>
                        <form action="{{ route('admin.avis.destroy', $avis->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
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
