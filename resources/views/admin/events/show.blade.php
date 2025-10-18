@extends('layouts.back')

@section('title', 'Détails de l\'Événement')

@section('content')
<div class="main-panel event-page">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">{{ $event->title }}</h4>
            <div>
                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary me-2">
                    <i class="mdi mdi-pencil"></i> Modifier
                </a>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                 class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: cover;">
                        @endif

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge badge-{{ $event->status === 'active' ? 'success' : ($event->status === 'draft' ? 'warning' : 'secondary') }} fs-6">
                                {{ App\Models\Event::getStatuses()[$event->status] }}
                            </span>
                            <span class="badge badge-info fs-6">
                                {{ App\Models\Event::getCategories()[$event->category] }}
                            </span>
                            @if($event->is_featured)
                                <span class="badge badge-warning fs-6">
                                    <i class="mdi mdi-star"></i> Mis en avant
                                </span>
                            @endif
                            @if($event->is_free)
                                <span class="badge badge-success fs-6">Gratuit</span>
                            @endif
                            @if($event->is_online)
                                <span class="badge badge-primary fs-6">En ligne</span>
                            @endif
                        </div>

                        @if($event->short_description)
                            <p class="lead">{{ $event->short_description }}</p>
                        @endif

                        <div class="mb-4">
                            {!! nl2br(e($event->description)) !!}
                        </div>

                        @if($event->eco_impact)
                            <div class="alert alert-success">
                                <h6><i class="mdi mdi-leaf"></i> Impact Écologique</h6>
                                <p class="mb-0">{{ $event->eco_impact }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Inscriptions -->
                @if($event->registrations->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Inscriptions ({{ $event->registrations->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Participant</th>
                                            <th>Email</th>
                                            <th>Date d'inscription</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->registrations as $registration)
                                            <tr>
                                                <td>{{ $registration->user->name }}</td>
                                                <td>{{ $registration->user->email }}</td>
                                                <td>{{ $registration->registered_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $registration->status === 'confirmed' ? 'success' : 'warning' }}">
                                                        {{ App\Models\EventRegistration::getStatuses()[$registration->status] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar avec informations détaillées -->
            <div class="col-md-4">
                <!-- Informations essentielles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informations Essentielles</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong><i class="mdi mdi-calendar"></i> Date et heure</strong><br>
                            <span>{{ $event->full_date }}</span>
                        </div>

                        @if($event->registration_deadline)
                            <div class="mb-3">
                                <strong><i class="mdi mdi-clock-alert"></i> Inscription jusqu'au</strong><br>
                                <span>{{ $event->registration_deadline->format('d/m/Y à H:i') }}</span>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong><i class="mdi mdi-map-marker"></i> Lieu</strong><br>
                            @if($event->is_online)
                                <span>En ligne</span>
                                @if($event->meeting_link)
                                    <br><a href="{{ $event->meeting_link }}" target="_blank" class="text-primary">
                                        <i class="mdi mdi-link"></i> Lien de la réunion
                                    </a>
                                @endif
                            @else
                                @if($event->location)
                                    <span>{{ $event->location }}</span><br>
                                @endif
                                @if($event->address)
                                    <span>{{ $event->address }}</span><br>
                                @endif
                                <span>{{ $event->city }}
                                    @if($event->postal_code)
                                        {{ $event->postal_code }}
                                    @endif
                                </span>

                                <!-- Carte de localisation -->
                                @if($event->latitude && $event->longitude)
                                    <div class="mt-3">
                                        <div id="map-viewer" style="height: 250px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong><i class="mdi mdi-account-group"></i> Participants</strong><br>
                            <span>{{ $event->current_participants }}
                                @if($event->max_participants)
                                    / {{ $event->max_participants }}
                                @endif
                                participant(s)
                            </span>
                            @if($event->max_participants)
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ ($event->current_participants / $event->max_participants) * 100 }}%">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong><i class="mdi mdi-cash"></i> Prix</strong><br>
                            <span class="fs-5 text-success">{{ $event->formatted_price }}</span>
                        </div>
                    </div>
                </div>

                <!-- Impact écologique -->
                @if($event->sustainability_score || $event->carbon_footprint)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="mdi mdi-leaf text-success"></i> Impact Écologique</h5>
                        </div>
                        <div class="card-body">
                            @if($event->sustainability_score)
                                <div class="mb-3">
                                    <strong>Score de durabilité</strong>
                                    <div class="d-flex align-items-center mt-1">
                                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $event->eco_impact_badge['class'] }}" 
                                                 role="progressbar" style="width: {{ $event->sustainability_score }}%">
                                                {{ $event->sustainability_score }}%
                                            </div>
                                        </div>
                                        <span class="badge badge-{{ $event->eco_impact_badge['class'] }}">
                                            {{ $event->eco_impact_badge['label'] }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if($event->carbon_footprint)
                                <div class="mb-3">
                                    <strong>Empreinte carbone</strong><br>
                                    <span class="text-muted">{{ $event->carbon_footprint }} kg CO2</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Organisateur -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Organisateur</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>{{ $event->organizer_name }}</strong>
                        </div>
                        <div class="mb-2">
                            <i class="mdi mdi-email"></i> 
                            <a href="mailto:{{ $event->organizer_email }}">{{ $event->organizer_email }}</a>
                        </div>
                        @if($event->organizer_phone)
                            <div class="mb-2">
                                <i class="mdi mdi-phone"></i> 
                                <a href="tel:{{ $event->organizer_phone }}">{{ $event->organizer_phone }}</a>
                            </div>
                        @endif
                        <div class="text-muted">
                            <small>Créé par {{ $event->organizer->name }} le {{ $event->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary">
                                <i class="mdi mdi-pencil"></i> Modifier
                            </a>
                            
                            <form action="{{ route('admin.events.duplicate', $event) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="mdi mdi-content-copy"></i> Dupliquer
                                </button>
                            </form>

                            <form action="{{ route('admin.events.toggle-featured', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="mdi mdi-star{{ $event->is_featured ? '-off' : '' }}"></i>
                                    {{ $event->is_featured ? 'Retirer' : 'Mettre' }} en avant
                                </button>
                            </form>

                            <hr>

                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="mdi mdi-delete"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser la carte de visualisation si les coordonnées sont disponibles
        @if(!$event->is_online && $event->latitude && $event->longitude)
            const mapViewer = window.initMapViewer({
                containerId: 'map-viewer',
                lat: {{ $event->latitude }},
                lng: {{ $event->longitude }},
                zoom: 15,
                popupText: '<strong>{{ $event->location ?? $event->title }}</strong><br>{{ $event->address }}'
            });
        @endif
    });
</script>
@endpush
@endsection
