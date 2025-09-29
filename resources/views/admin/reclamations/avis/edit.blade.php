@extends('layouts.back')

@section('title', 'Modifier un Avis - Réclamation #{{ $reclamation->id }}')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Modifier l'Avis #{{ $avis->id }} pour la Réclamation "{{ $reclamation->sujet }}"</h4>
                    <p class="card-description">
                        Modifiez les informations de l'avis.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.reclamations.avis.update', ['reclamation' => $reclamation->id, 'avis' => $avis->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="note">Note <span class="text-danger">*</span></label>
                            <select class="form-control @error('note') is-invalid @enderror" id="note" name="note" required>
                                <option value="">Choisir une note</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('note', $avis->note) == $i ? 'selected' : '' }}>{{ $i }} étoile{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="commentaire">Commentaire <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="5" required>{{ old('commentaire', $avis->commentaire) }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="user_id">Utilisateur <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $avis->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.reclamations.avis.index', $reclamation) }}" class="btn btn-secondary">
                                <i class="ti-arrow-left"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
