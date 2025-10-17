@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier la publication</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.publications.update', $publication->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="titre">Titre</label>
                            <input type="text" name="titre" id="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre', $publication->titre) }}" required>
                            @error('titre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contenu">Contenu</label>
                            <textarea name="contenu" id="contenu" class="form-control @error('contenu') is-invalid @enderror" rows="5" required>{{ old('contenu', $publication->contenu) }}</textarea>
                            @error('contenu')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            @if($publication->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $publication->image) }}" alt="Image actuelle" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror">
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection