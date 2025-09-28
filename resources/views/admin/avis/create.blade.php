@extends('layouts.back')

@section('title', 'Créer un Avis')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Nouvel Avis</h4>
                    <p class="card-description">
                        Remplissez le formulaire pour créer un nouvel avis.
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

                    <form action="{{ route('admin.avis.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="note">Note <span class="text-danger">*</span></label>
                            <select class="form-control @error('note') is-invalid @enderror" id="note" name="note" required>
                                <option value="">Choisir une note</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('note') == $i ? 'selected' : '' }}>{{ $i }} étoile{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="commentaire">Commentaire <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="5" required>{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="user_id">Utilisateur <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reclamation_id">Réclamation <span class="text-danger">*</span></label>
                            <select class="form-control @error('reclamation_id') is-invalid @enderror" id="reclamation_id" name="reclamation_id" required>
                                <option value="">Sélectionner une réclamation</option>
                                @foreach($reclamations as $reclamation)
                                    <option value="{{ $reclamation->id }}" {{ old('reclamation_id') == $reclamation->id ? 'selected' : '' }}>
                                        #{{ $reclamation->id }} - {{ $reclamation->sujet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reclamation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-save"></i> Créer
                            </button>
                            <a href="{{ route('admin.avis.index') }}" class="btn btn-secondary">
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
