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
                                        @if($reclamation->statut === 'en_attente')
                                            <span class="badge badge-secondary">En attente</span>
                                        @elseif($reclamation->statut === 'en_cours')
                                            <span class="badge badge-warning">En cours</span>
                                        @elseif($reclamation->statut === 'traitee')
                                            <span class="badge badge-success">Traitée</span>
                                        @endif
                                    </p>
                                    <p><strong>Créée le:</strong> {{ $reclamation->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Note moyenne:</strong> 
                                        @if($reclamation->avis->count() > 0)
                                            <span class="badge badge-info">{{ number_format($reclamation->averageRating(), 1) }}/5 ⭐</span>
                                        @else
                                            <span class="text-muted">Aucun avis</span>
                                        @endif
                                    </p>
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

                    <!-- Section Réponses -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti-comments"></i> Réponses ({{ $reclamation->responses->count() }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Historique des réponses -->
                                    @if($reclamation->responses->count() > 0)
                                        <div class="timeline mb-4">
                                            @foreach($reclamation->responses as $response)
                                                <div class="timeline-item mb-3 p-3 border-left border-primary" style="border-left-width: 3px !important; background-color: #f8f9fa;">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="ti-user"></i> 
                                                                {{ $response->user->name }}
                                                            </h6>
                                                            <small class="text-muted">
                                                                <i class="ti-time"></i>
                                                                {{ $response->created_at->format('d/m/Y à H:i') }}
                                                                ({{ $response->created_at->diffForHumans() }})
                                                            </small>
                                                        </div>
                                                        <form action="{{ route('admin.reclamations.responses.destroy', [$reclamation, $response]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette réponse ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $response->contenu }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-4">
                                            <i class="ti-info-alt"></i> Aucune réponse pour cette réclamation.
                                        </p>
                                    @endif

                                    <!-- Formulaire d'ajout de réponse -->
                                    <div class="border-top pt-4">
                                        <h6 class="mb-3">
                                            <i class="ti-comment-alt"></i> Ajouter une nouvelle réponse
                                        </h6>
                                        <form action="{{ route('admin.reclamations.responses.store', $reclamation) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="contenu">
                                                    <strong>Votre réponse:</strong>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <textarea 
                                                    name="contenu" 
                                                    id="contenu" 
                                                    class="form-control @error('contenu') is-invalid @enderror" 
                                                    rows="4" 
                                                    placeholder="Rédigez une réponse professionnelle..."
                                                    required>{{ old('contenu') }}</textarea>
                                                @error('contenu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="statut">
                                                    <strong>Mettre à jour le statut (optionnel):</strong>
                                                </label>
                                                <select name="statut" id="statut" class="form-control">
                                                    <option value="">-- Ne pas changer --</option>
                                                    <option value="en_attente" {{ $reclamation->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                                    <option value="en_cours" {{ $reclamation->statut == 'en_cours' ? 'selected' : '' }}>En cours de traitement</option>
                                                    <option value="traitee" {{ $reclamation->statut == 'traitee' ? 'selected' : '' }}>Traitée</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="ti-check"></i> Envoyer la réponse
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Avis (Notes uniquement) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti-star"></i> Avis ({{ $reclamation->avis->count() }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reclamation->avis->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Utilisateur</th>
                                                        <th>Note</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reclamation->avis as $avis)
                                                        <tr>
                                                            <td>{{ $avis->user->name ?? 'N/A' }}</td>
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
                                                            <td>{{ $avis->created_at->format('d/m/Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucun avis pour cette réclamation.</p>
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
                        <form action="{{ route('admin.reclamations.destroy', $reclamation) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">
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
