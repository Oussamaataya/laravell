@extends('layouts.back')

@section('title', 'Modifier une Réclamation')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Modifier la Réclamation #{{ $reclamation->id }}</h4>
                    <p class="card-description">
                        Modifiez les informations de la réclamation.
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

                    <form action="{{ route('admin.reclamations.update', $reclamation) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="sujet">Sujet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sujet') is-invalid @enderror" id="sujet" name="sujet" value="{{ old('sujet', $reclamation->sujet) }}" required>
                            @error('sujet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $reclamation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="statut">Statut <span class="text-danger">*</span></label>
                            <select class="form-control @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="">Choisir un statut</option>
                                <option value="en_attente" {{ old('statut', $reclamation->statut) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ old('statut', $reclamation->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="traitee" {{ old('statut', $reclamation->statut) == 'traitee' ? 'selected' : '' }}>Traité</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="user_id">Utilisateur <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $reclamation->user_id) == $user->id ? 'selected' : '' }}>
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
                            <a href="{{ route('admin.reclamations.index') }}" class="btn btn-secondary">
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
