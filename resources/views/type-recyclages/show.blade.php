@extends('layouts.back')

@section('title', 'Détails du Type de Recyclage')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">{{ $typeRecyclage->nom }}</h3>
                        <h6 class="font-weight-normal mb-0">Détails du type de recyclage</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex gap-2">
                            <a href="{{ route('admin.type-recyclages.edit', $typeRecyclage) }}" class="btn btn-primary btn-sm">
                                <i class="ti-pencil"></i> Modifier
                            </a>
                            <a href="{{ route('admin.type-recyclages.index') }}" class="btn btn-secondary btn-sm">
                                <i class="ti-arrow-left"></i> Retour
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
                        <div class="d-flex align-items-center mb-4">
                            @if($typeRecyclage->icone)
                                <i class="{{ $typeRecyclage->icone }} mr-3" style="color: {{ $typeRecyclage->couleur ?? '#28a745' }}; font-size: 2rem;"></i>
                            @endif
                            <div>
                                <h4 class="card-title mb-1">{{ $typeRecyclage->nom }}</h4>
                                <span class="badge {{ $typeRecyclage->actif ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $typeRecyclage->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>

                        @if($typeRecyclage->description)
                            <div class="mb-4">
                                <h6>Description :</h6>
                                <p class="text-muted">{{ $typeRecyclage->description }}</p>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Couleur :</h6>
                                @if($typeRecyclage->couleur)
                                    <div class="d-flex align-items-center">
                                        <div class="color-preview mr-2" style="width: 30px; height: 30px; background-color: {{ $typeRecyclage->couleur }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                        <span class="text-muted">{{ $typeRecyclage->couleur }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Aucune couleur définie</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>Icône :</h6>
                                @if($typeRecyclage->icone)
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $typeRecyclage->icone }} mr-2" style="font-size: 1.5rem;"></i>
                                        <span class="text-muted">{{ $typeRecyclage->icone }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Aucune icône définie</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Informations :</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Créé le :</small><br>
                                    <span>{{ $typeRecyclage->created_at->format('d/m/Y à H:i') }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Modifié le :</small><br>
                                    <span>{{ $typeRecyclage->updated_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Statistiques</h4>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Recyclages utilisant ce type :</span>
                                <span class="badge badge-info">{{ $typeRecyclage->recyclages->count() }}</span>
                            </div>
                            
                            @if($typeRecyclage->recyclages->count() > 0)
                                <div class="mt-4">
                                    <h6>Statuts des recyclages :</h6>
                                    @php
                                        $statuts = $typeRecyclage->recyclages->groupBy('statut');
                                    @endphp
                                    @foreach($statuts as $statut => $recyclages)
                                        <div class="d-flex justify-content-between mb-2">
                                            <small class="text-muted">{{ ucfirst($statut) }} :</small>
                                            <small class="badge 
                                                @if($statut === 'planifie') badge-primary
                                                @elseif($statut === 'en_cours') badge-warning
                                                @elseif($statut === 'termine') badge-success
                                                @else badge-secondary
                                                @endif">
                                                {{ $recyclages->count() }}
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($typeRecyclage->recyclages->count() > 0)
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Recyclages récents utilisant ce type</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Lieu</th>
                                            <th>Date</th>
                                            <th>Organisateur</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($typeRecyclage->recyclages->take(5) as $recyclage)
                                            <tr>
                                                <td>{{ $recyclage->titre }}</td>
                                                <td>{{ $recyclage->lieu }}</td>
                                                <td>{{ $recyclage->date_collecte->format('d/m/Y') }}</td>
                                                <td>{{ $recyclage->user->name }}</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($recyclage->statut === 'planifie') badge-primary
                                                        @elseif($recyclage->statut === 'en_cours') badge-warning
                                                        @elseif($recyclage->statut === 'termine') badge-success
                                                        @else badge-secondary
                                                        @endif">
                                                        {{ $recyclage->statut_formate }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('recyclages.show', $recyclage) }}" class="btn btn-outline-info btn-sm">
                                                        <i class="ti-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($typeRecyclage->recyclages->count() > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('recyclages.index') }}?type={{ $typeRecyclage->id }}" class="btn btn-outline-primary btn-sm">
                                        Voir tous les recyclages de ce type
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
