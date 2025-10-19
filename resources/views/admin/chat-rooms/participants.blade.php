@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.chat-rooms.index') }}">Chat Rooms</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.chat-rooms.show', $chatRoom->id) }}">{{ $chatRoom->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Participants</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users text-primary me-2"></i>
                Gestion des Participants
            </h1>
            <p class="text-muted mb-0">Gérez les membres de "{{ $chatRoom->name }}"</p>
        </div>
    </div>

    <!-- Informations de la room -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-{{ $chatRoom->type === 'public' ? 'primary' : 'warning' }} text-white rounded d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-{{ $chatRoom->type === 'public' ? 'globe' : 'lock' }} fa-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $chatRoom->name }}</h5>
                                    <p class="text-muted mb-1">{{ $chatRoom->description }}</p>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-{{ $chatRoom->type === 'public' ? 'primary' : 'warning' }}">
                                            {{ ucfirst($chatRoom->type) }}
                                        </span>
                                        <span class="badge bg-{{ $chatRoom->is_active ? 'success' : 'secondary' }}">
                                            {{ $chatRoom->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <span class="badge bg-info">
                                            <i class="fas fa-key me-1"></i>
                                            {{ $chatRoom->room_code }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stats-number text-primary">{{ $participants->total() }}</div>
                                    <div class="stats-label">Total</div>
                                </div>
                                <div class="col-4">
                                    <div class="stats-number text-success">{{ $participants->where('is_banned', false)->count() }}</div>
                                    <div class="stats-label">Actifs</div>
                                </div>
                                <div class="col-4">
                                    <div class="stats-number text-danger">{{ $participants->where('is_banned', true)->count() }}</div>
                                    <div class="stats-label">Bannis</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Liste des participants -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Liste des participants
                    </h5>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchParticipants" class="form-control" placeholder="Rechercher un participant...">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <i class="fas fa-user me-1"></i>
                                        Participant
                                    </th>
                                    <th width="120" class="text-center">
                                        <i class="fas fa-crown me-1"></i>
                                        Rôle
                                    </th>
                                    <th width="150" class="text-center">
                                        <i class="fas fa-calendar me-1"></i>
                                        Rejoint le
                                    </th>
                                    <th width="120" class="text-center">
                                        <i class="fas fa-eye me-1"></i>
                                        Dernière vue
                                    </th>
                                    <th width="100" class="text-center">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Statut
                                    </th>
                                    <th width="200" class="text-center">
                                        <i class="fas fa-cogs me-1"></i>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="participantsTable">
                                @forelse($participants as $participant)
                                <tr class="participant-row" data-name="{{ strtolower($participant->user->name) }}" data-email="{{ strtolower($participant->user->email) }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $participant->user->name }}</div>
                                                <small class="text-muted">{{ $participant->user->email }}</small>
                                                @if($participant->user->id === $chatRoom->created_by)
                                                    <br><span class="badge bg-warning small">
                                                        <i class="fas fa-star me-1"></i>Créateur
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $participant->role === 'admin' ? 'danger' : ($participant->role === 'moderator' ? 'warning' : 'secondary') }}">
                                            <i class="fas fa-{{ $participant->role === 'admin' ? 'crown' : ($participant->role === 'moderator' ? 'shield-alt' : 'user') }} me-1"></i>
                                            {{ ucfirst($participant->role) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small">
                                            {{ $participant->joined_at->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $participant->joined_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($participant->last_seen)
                                            <div class="small">
                                                {{ $participant->last_seen->format('d/m/Y') }}<br>
                                                <span class="text-muted">{{ $participant->last_seen->diffForHumans() }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small">Jamais vu</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($participant->is_banned)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban me-1"></i>Banni
                                            </span>
                                        @elseif($participant->is_muted)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-volume-mute me-1"></i>Muet
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Actif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if(!$participant->is_banned)
                                                <form action="{{ route('admin.chat-rooms.toggle-ban', [$chatRoom->id, $participant->user->id]) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Bannir"
                                                            onclick="return confirm('Bannir ce participant ?')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.chat-rooms.toggle-ban', [$chatRoom->id, $participant->user->id]) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-success" 
                                                            title="Débannir"
                                                            onclick="return confirm('Débannir ce participant ?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($participant->user->id !== $chatRoom->created_by)
                                                <form action="{{ route('admin.chat-rooms.remove-participant', [$chatRoom->id, $participant->user->id]) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer"
                                                            onclick="return confirm('Supprimer ce participant de la room ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h5>Aucun participant</h5>
                                            <p>Cette room n'a pas encore de participants.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($participants->hasPages())
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Affichage de {{ $participants->firstItem() }} à {{ $participants->lastItem() }} 
                                sur {{ $participants->total() }} participants
                            </div>
                            <div>
                                {{ $participants->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panneau d'ajout de participants -->
        <div class="col-lg-4">
            <!-- Ajouter un participant -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Ajouter un participant
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.chat-rooms.add-participant', $chatRoom->id) }}" method="POST" id="addParticipantForm">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>
                                Utilisateur
                            </label>
                            <select class="form-select select2-users @error('user_id') is-invalid @enderror" 
                                    id="user_id" 
                                    name="user_id" 
                                    required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">
                                <i class="fas fa-crown me-1"></i>
                                Rôle
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Sélectionner un rôle</option>
                                <option value="member">👤 Membre</option>
                                <option value="moderator">🛡️ Modérateur</option>
                                <option value="admin">👑 Administrateur</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-user-plus me-1"></i>
                            Ajouter le participant
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistiques détaillées -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition des rôles
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $roleStats = $participants->groupBy('role')->map->count();
                    @endphp
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stats-number text-danger">{{ $roleStats->get('admin', 0) }}</div>
                            <div class="stats-label">Admins</div>
                        </div>
                        <div class="col-4">
                            <div class="stats-number text-warning">{{ $roleStats->get('moderator', 0) }}</div>
                            <div class="stats-label">Modérateurs</div>
                        </div>
                        <div class="col-4">
                            <div class="stats-number text-secondary">{{ $roleStats->get('member', 0) }}</div>
                            <div class="stats-label">Membres</div>
                        </div>
                    </div>
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
                        <a href="{{ route('admin.chat-rooms.show', $chatRoom->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>
                            Voir les détails de la room
                        </a>
                        
                        <a href="{{ route('admin.chat-rooms.edit', $chatRoom->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Éditer la room
                        </a>
                        
                        <button type="button" class="btn btn-info" onclick="exportParticipants()">
                            <i class="fas fa-download me-1"></i>
                            Exporter la liste
                        </button>
                        
                        <form action="{{ route('admin.chat-rooms.regenerate-code', $chatRoom->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn btn-outline-secondary w-100"
                                    onclick="return confirm('Régénérer le code d\'invitation ?')">
                                <i class="fas fa-sync me-1"></i>
                                Régénérer le code
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Initialiser Select2 pour la sélection d'utilisateurs
    if (typeof $ !== 'undefined' && $('.select2-users').length) {
        $('.select2-users').select2({
            placeholder: '🔍 Rechercher un utilisateur...',
            allowClear: true,
            width: '100%',
            templateResult: function(user) {
                if (user.loading) {
                    return user.text;
                }
                return $('<span><i class="fas fa-user me-2"></i>' + user.text + '</span>');
            }
        });
    }

    // Recherche en temps réel des participants
    const searchInput = document.getElementById('searchParticipants');
    const participantRows = document.querySelectorAll('.participant-row');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        participantRows.forEach(row => {
            const name = row.dataset.name;
            const email = row.dataset.email;
            
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Validation du formulaire d'ajout
    const addForm = document.getElementById('addParticipantForm');
    addForm.addEventListener('submit', function(e) {
        const userId = document.getElementById('user_id').value;
        const role = document.getElementById('role').value;

        if (!userId) {
            e.preventDefault();
            alert('⚠️ Veuillez sélectionner un utilisateur.');
            return;
        }

        if (!role) {
            e.preventDefault();
            alert('⚠️ Veuillez sélectionner un rôle.');
            return;
        }

        // Confirmation
        const userName = $('#user_id option:selected').text();
        const confirmMessage = `Ajouter ${userName} avec le rôle ${role} ?`;
        if (!confirm(confirmMessage)) {
            e.preventDefault();
        }
    });

    // Fonction d'export des participants
    window.exportParticipants = function() {
        const url = '{{ route("admin.chat-rooms.export-participants", $chatRoom->id) }}';
        window.open(url, '_blank');
    };

    // Auto-refresh des statistiques toutes les 60 secondes
    setInterval(function() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                // Mettre à jour les compteurs
                document.querySelectorAll('.stats-number').forEach((element, index) => {
                    if (data.stats[index] !== undefined) {
                        element.textContent = data.stats[index];
                    }
                });
            }
        })
        .catch(error => console.log('Erreur lors de la mise à jour:', error));
    }, 60000);
});
</script>

<style>
.stats-number {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stats-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.avatar-sm {
    flex-shrink: 0;
}

.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.participant-row {
    transition: all 0.3s ease;
}

.participant-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group .btn {
    margin-right: 2px;
}
</style>
@endpush
