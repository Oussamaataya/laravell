@extends('layouts.app')

@section('title', $publication->titre)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('publications.index') }}">Publications</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $publication->titre }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="card-title h2">{{ $publication->titre }}</h1>
                <div>
                    @auth
                        @if(auth()->user()->id === $publication->user_id || auth()->user()->role === 'admin')
                            <a href="{{ route('publications.edit', $publication) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('publications.destroy', $publication) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($publication->user->name) }}&background=random" class="rounded-circle" width="40" height="40" alt="{{ $publication->user->name }}">
                </div>
                <div>
                    <div class="fw-bold">{{ $publication->user->name }}</div>
                    <div class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i> Publié le {{ $publication->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>

            @if($publication->image)
                <div class="text-center my-4">
                    <img src="{{ asset('storage/' . $publication->image) }}" class="img-fluid rounded" alt="{{ $publication->titre }}" style="max-height: 400px;">
                </div>
            @endif

            <div class="my-4">
                <p class="lead">{{ $publication->contenu }}</p>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top border-bottom py-3 my-4">
                <div>
                    <span class="me-3">
                        <i class="fas fa-comment text-muted"></i> {{ $publication->commentaires->count() }} commentaires
                    </span>
                </div>
                <div>
                    @auth
                        <form action="{{ route('publications.like', $publication) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $publication->likedBy(auth()->user()) ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-heart"></i> 
                                {{ $publication->likes->count() }} J'aime
                            </button>
                        </form>
                    @else
                        <span class="text-muted">
                            <i class="fas fa-heart"></i> {{ $publication->likes->count() }} J'aime
                        </span>
                    @endauth
                </div>
            </div>

            <!-- Section commentaires -->
            <div class="mt-5">
                <h3 class="h4 mb-4">Commentaires ({{ $publication->commentaires->count() }})</h3>
                
                @auth
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="{{ route('commentaires.store', $publication) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="contenu" class="form-label">Ajouter un commentaire</label>
                                    <textarea class="form-control @error('contenu') is-invalid @enderror" id="contenu" name="contenu" rows="3" required>{{ old('contenu') }}</textarea>
                                    @error('contenu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Publier</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <a href="{{ route('login') }}">Connectez-vous</a> pour ajouter un commentaire.
                    </div>
                @endauth

                <div class="commentaires-list">
                    @forelse($publication->commentaires->sortByDesc('created_at') as $commentaire)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($commentaire->user->name) }}&background=random" class="rounded-circle me-2" width="30" height="30" alt="{{ $commentaire->user->name }}">
                                        <span class="fw-bold">{{ $commentaire->user->name }}</span>
                                    </div>
                                    <small class="text-muted">{{ $commentaire->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="card-text">{{ $commentaire->contenu }}</p>
                                
                                @auth
                                    @if(auth()->user()->id === $commentaire->user_id || auth()->user()->role === 'admin')
                                        <div class="d-flex justify-content-end mt-2">
                                            <button class="btn btn-sm btn-link text-decoration-none edit-comment-btn" data-id="{{ $commentaire->id }}" data-content="{{ $commentaire->contenu }}">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            <form action="{{ route('commentaires.destroy', $commentaire) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="edit-comment-form d-none mt-3" id="edit-form-{{ $commentaire->id }}">
                                            <form action="{{ route('commentaires.update', $commentaire) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <textarea class="form-control" name="contenu" rows="2" required>{{ $commentaire->contenu }}</textarea>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 cancel-edit-btn">Annuler</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light">
                            Aucun commentaire pour le moment. Soyez le premier à commenter!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'édition des commentaires
        const editBtns = document.querySelectorAll('.edit-comment-btn');
        const cancelBtns = document.querySelectorAll('.cancel-edit-btn');
        
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const commentId = this.dataset.id;
                const formElement = document.getElementById(`edit-form-${commentId}`);
                formElement.classList.remove('d-none');
                this.closest('.card-body').querySelector('.card-text').classList.add('d-none');
                this.closest('.d-flex').classList.add('d-none');
            });
        });
        
        cancelBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const formElement = this.closest('.edit-comment-form');
                formElement.classList.add('d-none');
                const cardBody = formElement.closest('.card-body');
                cardBody.querySelector('.card-text').classList.remove('d-none');
                cardBody.querySelector('.d-flex.justify-content-end').classList.remove('d-none');
            });
        });
    });
</script>
@endpush
@endsection