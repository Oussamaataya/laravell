@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Commentaires</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Publication</th>
                                <th>Auteur</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commentaires as $commentaire)
                            <tr>
                                <td>{{ $commentaire->id }}</td>
                                <td>{{ $commentaire->publication->titre ?? '-' }}</td>
                                <td>{{ $commentaire->user->name ?? '-' }}</td>
                                <td>{{ $commentaire->contenu }}</td>
                                <td>{{ $commentaire->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.commentaires.destroy', $commentaire->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce commentaire ?')">
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
