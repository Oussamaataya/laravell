@extends('layouts.back')

@section('title', 'Créer une Réclamation')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Nouvelle Réclamation</h4>
                    <p class="card-description">
                        Remplissez le formulaire pour créer une nouvelle réclamation.
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

                    <form action="{{ route('admin.reclamations.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="sujet">Sujet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sujet') is-invalid @enderror" id="sujet" name="sujet" value="{{ old('sujet') }}" >
                            @error('sujet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="statut">Statut <span class="text-danger">*</span></label>
                            <select class="form-control @error('statut') is-invalid @enderror" id="statut" name="statut" >
                                <option value="">Choisir un statut</option>
                                <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="traitee" {{ old('statut') == 'traitee' ? 'selected' : '' }}>Traité</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="user_id">Utilisateur <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" >
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
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-save"></i> Créer
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
