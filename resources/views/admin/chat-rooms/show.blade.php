@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.chat-rooms.index') }}">Chat Rooms</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $chatRoom->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-{{ $chatRoom->type === 'public' ? 'globe' : 'lock' }} text-{{ $chatRoom->type === 'public' ? 'primary' : 'warning' }} me-2"></i>
                        {{ $chatRoom->name }}
                    </h1>
                    <p class="text-muted mb-0">Détails et gestion de la chat room</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-{{ $chatRoom->is_active ? 'success' : 'secondary' }} fs-6 px-3 py-2">
                        <i class="fas fa-{{ $chatRoom->is_active ? 'check-circle' : 'pause-circle' }} me-1"></i>
                        {{ $chatRoom->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <a href="{{ route('admin.chat-rooms.edit', $chatRoom->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        Éditer
                    </a>
                    <a href="{{ route('admin.chat-rooms.participants', $chatRoom->id) }}" class="btn btn-primary">
                        <i class="fas fa-users me-1"></i>
                        Participants
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nom de la room</label>
                                <div class="fs-5">{{ $chatRoom->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Type</label>
                                <div>
                                    <span class="badge bg-{{ $chatRoom->type === 'public' ? 'primary' : 'warning' }} fs-6">
                                        <i class="fas fa-{{ $chatRoom->type === 'public' ? 'globe' : 'lock' }} me-1"></i>
                                        {{ ucfirst($chatRoom->type) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Créateur</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($chatRoom->creator->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $chatRoom->creator->name }}</div>
                                        <small class="text-muted">{{ $chatRoom->creator->email }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Code d'invitation</label>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="bg-light p-2 rounded">{{ $chatRoom->room_code }}</code>
                                    <form action="{{ route('admin.chat-rooms.regenerate-code', $chatRoom->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                onclick="return confirm('Régénérer le code d\'invitation ?')"
                                                title="Régénérer le code">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Limite de participants</label>
                                <div>{{ $chatRoom->max_participants ?? 'Illimitée' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Créée le</label>
                                <div>
                                    {{ $chatRoom->created_at->format('d/m/Y à H:i') }}
                                    <small class="text-muted">({{ $chatRoom->created_at->diffForHumans() }})</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Dernière activité</label>
                                <div>
                                    @if($chatRoom->last_activity)
                                        {{ $chatRoom->last_activity->format('d/m/Y à H:i') }}
                                        <small class="text-muted">({{ $chatRoom->last_activity->diffForHumans() }})</small>
                                    @else
                                        <span class="text-muted">Aucune activité</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($chatRoom->description)
                    <div class="mt-3">
                        <label class="form-label fw-bold text-muted">Description</label>
                        <div class="bg-light p-3 rounded">{{ $chatRoom->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Statistiques -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stats-number text-primary">{{ $stats['total_messages'] }}</div>
                            <div class="stats-label">Messages total</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-number text-success">{{ $stats['active_participants'] }}</div>
                            <div class="stats-label">Participants actifs</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-number text-info">{{ $stats['messages_today'] }}</div>
                            <div class="stats-label">Messages aujourd'hui</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-number text-warning">{{ $stats['messages_week'] }}</div>
                            <div class="stats-label">Messages cette semaine</div>
                        </div>
                    </div>
                    @if($stats['banned_participants'] > 0)
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ $stats['banned_participants'] }} participant(s) banni(s)
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.chat-rooms.toggle-status', $chatRoom->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $chatRoom->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $chatRoom->is_active ? 'pause' : 'play' }} me-1"></i>
                                {{ $chatRoom->is_active ? 'Désactiver' : 'Activer' }} la room
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.chat-rooms.participants', $chatRoom->id) }}" class="btn btn-primary w-100">
                            <i class="fas fa-users me-1"></i>
                            Gérer les participants
                        </a>
                        
                        <button type="button" class="btn btn-info w-100" onclick="exportMessages()">
                            <i class="fas fa-download me-1"></i>
                            Exporter les messages
                        </button>
                        
                        <form action="{{ route('admin.chat-rooms.destroy', $chatRoom->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette room et tous ses messages ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-1"></i>
                                Supprimer la room
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants récents -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Participants récents
                    </h6>
                    <a href="{{ route('admin.chat-rooms.participants', $chatRoom->id) }}" class="btn btn-sm btn-outline-primary">
                        Voir tous
                    </a>
                </div>
                <div class="card-body">
                    @forelse($chatRoom->participants->take(5) as $participant)
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar-sm me-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 32px; height: 32px;">
                                {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $participant->user->name }}</div>
                            <small class="text-muted">{{ $participant->user->email }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $participant->role === 'admin' ? 'danger' : ($participant->role === 'moderator' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($participant->role) }}
                            </span>
                            @if($participant->is_banned)
                                <br><span class="badge bg-danger small">Banni</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Aucun participant</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Messages récents -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Messages récents
                    </h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($chatRoom->messages as $message)
                    <div class="d-flex align-items-start mb-3">
                        <div class="avatar-sm me-2">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ strtoupper(substr($message->user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fw-medium me-2">{{ $message->user->name }}</span>
                                <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                            </div>
                            <div class="small">{{ Str::limit($message->message, 100) }}</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Aucun message</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Fonction d'export des messages
    window.exportMessages = function() {
        const url = '{{ route("admin.chat-rooms.export-messages", $chatRoom->id) }}';
        window.open(url, '_blank');
    };

    // Auto-refresh des statistiques toutes les 30 secondes
    setInterval(function() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                // Mettre à jour les statistiques
                document.querySelector('.stats-number.text-primary').textContent = data.stats.total_messages;
                document.querySelector('.stats-number.text-success').textContent = data.stats.active_participants;
                document.querySelector('.stats-number.text-info').textContent = data.stats.messages_today;
                document.querySelector('.stats-number.text-warning').textContent = data.stats.messages_week;
            }
        })
        .catch(error => console.log('Erreur lors de la mise à jour:', error));
    }, 30000);
});
</script>
@endpush
