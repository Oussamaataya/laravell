@extends('layouts.back')

@section('title', 'Modifier l\'Événement')

@section('content')
<div class="main-panel event-page">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card event-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Modifier l'Événement</h4>
                    <div>
                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-info me-2">
                            <i class="mdi mdi-eye"></i> Voir
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row event-form">
                        <!-- Informations de base -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Informations Générales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Titre de l'événement *</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $event->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="short_description" class="form-label">Description courte</label>
                                        <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                                  id="short_description" name="short_description" rows="2" 
                                                  maxlength="500">{{ old('short_description', $event->short_description) }}</textarea>
                                        <div class="form-text">Maximum 500 caractères</div>
                                        @error('short_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description complète *</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" name="description" rows="6" required>{{ old('description', $event->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image principale</label>
                                        @if($event->image)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($event->image) }}" alt="Image actuelle" 
                                                     class="img-thumbnail" style="max-height: 100px;">
                                                <small class="text-muted d-block">Image actuelle</small>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                               id="image" name="image" accept="image/*">
                                        <div class="form-text">Formats acceptés: JPG, PNG, GIF (max 2MB). Laisser vide pour conserver l'image actuelle.</div>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Date et heure -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Date et Heure</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Date de début *</label>
                                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                       id="start_date" name="start_date" 
                                                       value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Date de fin *</label>
                                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                       id="end_date" name="end_date" 
                                                       value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required>
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_time" class="form-label">Heure de début *</label>
                                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                                       id="start_time" name="start_time" 
                                                       value="{{ old('start_time', $event->start_time) }}" required>
                                                @error('start_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_time" class="form-label">Heure de fin *</label>
                                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                                       id="end_time" name="end_time" 
                                                       value="{{ old('end_time', $event->end_time) }}" required>
                                                @error('end_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registration_deadline" class="form-label">Date limite d'inscription</label>
                                        <input type="datetime-local" class="form-control @error('registration_deadline') is-invalid @enderror" 
                                               id="registration_deadline" name="registration_deadline" 
                                               value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i')) }}">
                                        @error('registration_deadline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Localisation -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Localisation</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_online" name="is_online" 
                                               value="1" {{ old('is_online', $event->is_online) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_online">
                                            Événement en ligne
                                        </label>
                                    </div>

                                    <div id="online_fields" style="display: {{ old('is_online', $event->is_online) ? 'block' : 'none' }};">
                                        <div class="mb-3">
                                            <label for="meeting_link" class="form-label">Lien de la réunion</label>
                                            <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                                                   id="meeting_link" name="meeting_link" 
                                                   value="{{ old('meeting_link', $event->meeting_link) }}">
                                            @error('meeting_link')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div id="physical_fields" style="display: {{ old('is_online', $event->is_online) ? 'none' : 'block' }};">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Nom du lieu</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                                   id="location" name="location" value="{{ old('location', $event->location) }}">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">Adresse complète</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="2" readonly>{{ old('address', $event->address) }}</textarea>
                                            <div class="form-text">Cliquez sur la carte pour sélectionner la localisation</div>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="city" class="form-label">Ville *</label>
                                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                           id="city" name="city" value="{{ old('city', $event->city) }}" 
                                                           {{ !old('is_online', $event->is_online) ? 'required' : '' }} readonly>
                                                    @error('city')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="postal_code" class="form-label">Code postal</label>
                                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                                           id="postal_code" name="postal_code" value="{{ old('postal_code', $event->postal_code) }}">
                                                    @error('postal_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Coordonnées GPS (cachés) -->
                                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $event->latitude ?? '36.8065') }}">
                                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $event->longitude ?? '10.1815') }}">

                                        <!-- Carte interactive -->
                                        <div class="mb-3">
                                            <label class="form-label">Sélectionner la localisation sur la carte</label>
                                            <div id="map" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                                            <div class="form-text mt-2">
                                                <i class="mdi mdi-information-outline"></i> 
                                                Cliquez sur la carte ou déplacez le marqueur pour définir la localisation exacte de l'événement.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-md-4">
                            <!-- Paramètres -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Paramètres</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Statut *</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            @foreach($statuses as $key => $status)
                                                <option value="{{ $key }}" {{ old('status', $event->status) == $key ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Catégorie *</label>
                                        <select class="form-select @error('category') is-invalid @enderror" 
                                                id="category" name="category" required>
                                            <option value="">Sélectionner une catégorie</option>
                                            @foreach($categories as $key => $category)
                                                <option value="{{ $key }}" {{ old('category', $event->category) == $key ? 'selected' : '' }}>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Mettre en avant
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Participants et Prix -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Participants et Prix</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="max_participants" class="form-label">Nombre max de participants</label>
                                        <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                               id="max_participants" name="max_participants" 
                                               value="{{ old('max_participants', $event->max_participants) }}" min="1">
                                        <div class="form-text">Laisser vide pour illimité</div>
                                        @error('max_participants')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="alert alert-info">
                                        <small><strong>Participants actuels:</strong> {{ $event->current_participants }}</small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_free" name="is_free" 
                                               value="1" {{ old('is_free', $event->is_free) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_free">
                                            Événement gratuit
                                        </label>
                                    </div>

                                    <div id="price_field" style="display: {{ old('is_free', $event->is_free) ? 'none' : 'block' }};">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Prix (€)</label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price', $event->price) }}" 
                                                   min="0" step="0.01">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Impact Écologique -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Impact Écologique</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="sustainability_score" class="form-label">Score de durabilité (0-100)</label>
                                        <input type="number" class="form-control @error('sustainability_score') is-invalid @enderror" 
                                               id="sustainability_score" name="sustainability_score" 
                                               value="{{ old('sustainability_score', $event->sustainability_score) }}" 
                                               min="0" max="100">
                                        @error('sustainability_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="carbon_footprint" class="form-label">Empreinte carbone (kg CO2)</label>
                                        <input type="number" class="form-control @error('carbon_footprint') is-invalid @enderror" 
                                               id="carbon_footprint" name="carbon_footprint" 
                                               value="{{ old('carbon_footprint', $event->carbon_footprint) }}" 
                                               min="0" step="0.01">
                                        @error('carbon_footprint')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="eco_impact" class="form-label">Description de l'impact écologique</label>
                                        <textarea class="form-control @error('eco_impact') is-invalid @enderror" 
                                                  id="eco_impact" name="eco_impact" rows="3">{{ old('eco_impact', $event->eco_impact) }}</textarea>
                                        @error('eco_impact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Organisateur -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Organisateur</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="organizer_name" class="form-label">Nom de l'organisateur *</label>
                                        <input type="text" class="form-control @error('organizer_name') is-invalid @enderror" 
                                               id="organizer_name" name="organizer_name" 
                                               value="{{ old('organizer_name', $event->organizer_name) }}" required>
                                        @error('organizer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="organizer_email" class="form-label">Email de l'organisateur *</label>
                                        <input type="email" class="form-control @error('organizer_email') is-invalid @enderror" 
                                               id="organizer_email" name="organizer_email" 
                                               value="{{ old('organizer_email', $event->organizer_email) }}" required>
                                        @error('organizer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="organizer_phone" class="form-label">Téléphone de l'organisateur</label>
                                        <input type="tel" class="form-control @error('organizer_phone') is-invalid @enderror" 
                                               id="organizer_phone" name="organizer_phone" 
                                               value="{{ old('organizer_phone', $event->organizer_phone) }}">
                                        @error('organizer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">Annuler</a>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    let mapSelector = null;

    // Gestion des champs en ligne/physique
    document.getElementById('is_online').addEventListener('change', function() {
        const onlineFields = document.getElementById('online_fields');
        const physicalFields = document.getElementById('physical_fields');
        const cityField = document.getElementById('city');
        
        if (this.checked) {
            onlineFields.style.display = 'block';
            physicalFields.style.display = 'none';
            cityField.removeAttribute('required');
            if (mapSelector) {
                mapSelector.destroy();
                mapSelector = null;
            }
        } else {
            onlineFields.style.display = 'none';
            physicalFields.style.display = 'block';
            cityField.setAttribute('required', 'required');
            initMap();
        }
    });

    // Gestion du prix gratuit
    document.getElementById('is_free').addEventListener('change', function() {
        const priceField = document.getElementById('price_field');
        const priceInput = document.getElementById('price');
        
        if (this.checked) {
            priceField.style.display = 'none';
            priceInput.value = '0';
        } else {
            priceField.style.display = 'block';
        }
    });

    // Synchroniser les dates
    document.getElementById('start_date').addEventListener('change', function() {
        const endDate = document.getElementById('end_date');
        if (!endDate.value || endDate.value < this.value) {
            endDate.value = this.value;
        }
        endDate.setAttribute('min', this.value);
    });

    // Initialiser la carte
    function initMap() {
        if (mapSelector) return; // Déjà initialisée

        const lat = parseFloat(document.getElementById('latitude').value) || 36.8065;
        const lng = parseFloat(document.getElementById('longitude').value) || 10.1815;

        mapSelector = window.initMapSelector({
            containerId: 'map',
            defaultLat: lat,
            defaultLng: lng,
            zoom: 13,
            latInputId: 'latitude',
            lngInputId: 'longitude',
            addressInputId: 'address',
            cityInputId: 'city'
        });
    }

    // Initialiser l'état des champs au chargement
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser la carte si le mode n'est pas en ligne
        if (!document.getElementById('is_online').checked) {
            initMap();
        }
    });
</script>
@endpush
@endsection
