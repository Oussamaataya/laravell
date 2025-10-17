@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Publications</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.publications.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Publication
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Date de création</th>
                                <th>Status</th>
                                <th>Commentaires</th>
                                <th>Likes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($publications as $publication)
                            <tr>
                                <td>{{ $publication->id }}</td>
                                <td>{{ $publication->titre }}</td>
                                <td>{{ $publication->user->name }}</td>
                                <td>{{ $publication->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.publications.approve', $publication->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $publication->is_approved ? 'btn-success' : 'btn-warning' }}">
                                            {{ $publication->is_approved ? 'Approuvé' : 'En attente' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $publication->commentaires->count() }}</td>
                                <td>{{ $publication->likes->count() }}</td>
                                <td>
                                    <a href="{{ route('admin.publications.edit', $publication->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.publications.destroy', $publication->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection