@extends('layouts.app')

@section('title', 'Publications')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold">Publications</h1>
            <p class="lead">Découvrez les dernières publications de notre communauté</p>
        </div>
        <div class="col-md-4 text-end align-self-center">
            @auth
                <a href="{{ route('publications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Nouvelle publication
                </a>
            @endauth
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($publications as $publication)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    @if($publication->image)
                        @php
                            $imgSrc = null;
                            // if image exists on the public disk (storage/app/public)
                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($publication->image)) {
                                $imgSrc = asset('storage/' . $publication->image);
                            }
                            // else if it's a legacy public path like 'images/publications/..'
                            elseif (file_exists(public_path($publication->image))) {
                                $imgSrc = asset($publication->image);
                            }
                        @endphp
                        @if($imgSrc)
                            <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $publication->titre }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light text-center py-5">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                    @else
                        <div class="bg-light text-center py-5">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $publication->titre }}</h5>
                        <p class="card-text text-muted">
                            <small>
                                <i class="fas fa-user me-1"></i> {{ $publication->user->name }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-calendar me-1"></i> {{ $publication->created_at->format('d/m/Y') }}
                            </small>
                        </p>
                        <p class="card-text">{{ Str::limit($publication->contenu, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-3">
                                    <i class="fas fa-comment text-muted"></i> {{ $publication->commentaires->count() }}
                                </span>
                                <span>
                                    <i class="fas fa-heart text-danger"></i> {{ $publication->likes->count() }}
                                </span>
                            </div>
                            <a href="{{ route('publications.show', $publication) }}" class="btn btn-sm btn-outline-primary">Lire plus</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Aucune publication disponible pour le moment.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $publications->links() }}
    </div>
</div>
@endsection