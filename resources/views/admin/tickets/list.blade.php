@extends('layouts.back')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Billets de l'événement</h3>
                    <h6 class="font-weight-normal mb-0">{{ $event->title }}</h6>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="justify-content-end d-flex">
                        <a href="{{ route('admin.tickets.scan', $event->id) }}" class="btn btn-primary">
                            <i class="mdi mdi-qrcode-scan"></i> Scanner les billets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">Total Billets</h4>
                    <h2 class="mb-5">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">Présents</h4>
                    <h2 class="mb-5">{{ $stats['checked_in'] }}</h2>
                    <small>{{ $stats['total'] > 0 ? round(($stats['checked_in']/$stats['total'])*100, 1) : 0 }}% du total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-warning card-img-holder text-white">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">En Attente</h4>
                    <h2 class="mb-5">{{ $stats['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">Annulés</h3>
                    <h2 class="mb-5">{{ $stats['cancelled'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <select name="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                                <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Utilisés</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulés</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher par nom..." value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-filter"></i> Filtrer
                        </button>
                        <a href="{{ route('admin.tickets.event', $event->id) }}" class="btn btn-secondary ml-2">
                            <i class="mdi mdi-refresh"></i> Réinitialiser
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des billets -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Liste des billets</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Code Billet</th>
                                    <th>Participant</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Inscrit le</th>
                                    <th>Check-in</th>
                                    <th>QR Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($event->registrations as $registration)
                                    @if(
                                        (request('status') && $registration->ticket_status == request('status')) || 
                                        !request('status')
                                    )
                                    @if(
                                        !request('search') || 
                                        stripos($registration->user->name, request('search')) !== false ||
                                        stripos($registration->user->email, request('search')) !== false ||
                                        stripos($registration->ticket_code, request('search')) !== false
                                    )
                                    <tr>
                                        <td>
                                            <strong>{{ $registration->ticket_code }}</strong>
                                        </td>
                                        <td>{{ $registration->user->name }}</td>
                                        <td>{{ $registration->user->email }}</td>
                                        <td>
                                            @if($registration->ticket_status === 'active')
                                                <span class="badge badge-warning">Actif</span>
                                            @elseif($registration->ticket_status === 'used')
                                                <span class="badge badge-success">Utilisé</span>
                                            @else
                                                <span class="badge badge-danger">Annulé</span>
                                            @endif
                                        </td>
                                        <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($registration->checked_in_at)
                                                <span class="text-success">
                                                    <i class="mdi mdi-check-circle"></i>
                                                    {{ $registration->checked_in_at->format('d/m/Y H:i') }}
                                                </span>
                                                @if($registration->checkedInBy)
                                                    <br>
                                                    <small class="text-muted">Par: {{ $registration->checkedInBy->name }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Non scanné</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($registration->qr_code_path)
                                                <img src="{{ Storage::url($registration->qr_code_path) }}" 
                                                     alt="QR Code" 
                                                     style="width: 50px; height: 50px;"
                                                     data-toggle="modal" 
                                                     data-target="#qrModal{{ $registration->id }}"
                                                     style="cursor: pointer;">
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($registration->qr_code_path)
                                                    <a href="{{ route('admin.tickets.download', $registration->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Télécharger QR">
                                                        <i class="mdi mdi-download"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($registration->ticket_status !== 'cancelled')
                                                    <form action="{{ route('admin.tickets.regenerate', $registration->id) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-info" 
                                                                title="Régénérer QR"
                                                                onclick="return confirm('Régénérer le QR Code ?')">
                                                            <i class="mdi mdi-refresh"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.tickets.cancel', $registration->id) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Annuler le billet"
                                                                onclick="return confirm('Annuler ce billet ?')">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal pour afficher le QR Code en grand -->
                                    <div class="modal fade" id="qrModal{{ $registration->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">QR Code - {{ $registration->ticket_code }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    @if($registration->qr_code_path)
                                                        <img src="{{ Storage::url($registration->qr_code_path) }}" 
                                                             alt="QR Code" 
                                                             class="img-fluid mb-3">
                                                    @endif
                                                    <p><strong>Participant:</strong> {{ $registration->user->name }}</p>
                                                    <p><strong>Code:</strong> {{ $registration->ticket_code }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="{{ route('admin.tickets.download', $registration->id) }}" 
                                                       class="btn btn-primary">
                                                        <i class="mdi mdi-download"></i> Télécharger
                                                    </a>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun billet trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
