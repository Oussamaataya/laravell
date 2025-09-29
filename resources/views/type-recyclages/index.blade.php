@extends('layouts.back')

@section('title', 'Gestion des Types de Recyclage')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Types de Recyclage</h3>
                        <h6 class="font-weight-normal mb-0">Gestion des types de matériaux recyclables</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <a href="{{ route('admin.type-recyclages.create') }}" class="btn btn-success btn-sm">
                                <i class="ti-plus"></i> Nouveau Type
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title mb-0">Liste des Types de Recyclage</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Couleur</th>
                                        <th>Icône</th>
                                        <th>Statut</th>
                                        <th>Recyclages</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($typeRecyclages as $type)
                                        <tr>
                                            <td>
                                                <strong>{{ $type->nom }}</strong>
                                            </td>
                                            <td>
                                                {{ Str::limit($type->description, 50) }}
                                            </td>
                                            <td>
                                                @if($type->couleur)
                                                    <span class="badge" style="background-color: {{ $type->couleur }}; color: white;">
                                                        {{ $type->couleur }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($type->icone)
                                                    <i class="{{ $type->icone }}"></i> {{ $type->icone }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($type->actif)
                                                    <span class="badge badge-success">Actif</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $type->recyclages_count }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.type-recyclages.show', $type) }}" 
                                                       class="btn btn-outline-info btn-sm" title="Voir">
                                                        <i class="ti-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.type-recyclages.edit', $type) }}" 
                                                       class="btn btn-outline-primary btn-sm" title="Modifier">
                                                        <i class="ti-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.type-recyclages.destroy', $type) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de recyclage ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="py-4">
                                                    <i class="ti-package text-muted" style="font-size: 3rem;"></i>
                                                    <p class="text-muted mt-2">Aucun type de recyclage trouvé</p>
                                                    <a href="{{ route('admin.type-recyclages.create') }}" class="btn btn-primary btn-sm">
                                                        <i class="ti-plus"></i> Créer le premier type
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($typeRecyclages->hasPages())
                            <div class="mt-3">
                                {{ $typeRecyclages->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
