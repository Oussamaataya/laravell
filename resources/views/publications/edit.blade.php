@extends('layouts.app')

@section('title', 'Modifier la publication')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('publications.index') }}">Publications</a></li>
            <li class="breadcrumb-item"><a href="{{ route('publications.show', $publication) }}">{{ $publication->titre }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Modifier la publication</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('publications.update', $publication) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $publication->titre) }}" required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('contenu') is-invalid @enderror" id="contenu" name="contenu" rows="6" required>{{ old('contenu', $publication->contenu) }}</textarea>
                            @error('contenu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            @if($publication->image)
                                @php
                                    $imgSrc = null;
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($publication->image)) {
                                        $imgSrc = asset('storage/' . $publication->image);
                                    } elseif (file_exists(public_path($publication->image))) {
                                        $imgSrc = asset($publication->image);
                                    }
                                @endphp
                                @if($imgSrc)
                                    <div class="mb-2">
                                        <img src="{{ $imgSrc }}" alt="{{ $publication->titre }}" class="img-thumbnail" style="max-height: 200px;">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image">
                                            <label class="form-check-label" for="delete_image">
                                                Supprimer l'image actuelle
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('publications.show', $publication) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection