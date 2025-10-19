@extends('layouts.app')

@section('title', 'Chat - Rooms de discussion')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Sidebar avec les rooms --}}
        <div class="col-md-4 col-lg-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Chat Rooms</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    {{-- Barre de recherche --}}
                    <div class="p-3 border-bottom">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="searchRooms" placeholder="Rechercher une room...">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#joinByCodeModal">
                                <i class="fas fa-key"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Mes rooms --}}
                    @if($userRooms->count() > 0)
                    <div class="p-3 border-bottom">
                        <h6 class="text-muted mb-2">Mes Rooms</h6>
                        <div id="userRooms">
                            @foreach($userRooms as $room)
                            <div class="room-item mb-2 p-2 rounded" data-room-id="{{ $room->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $room->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> {{ $room->active_participants_count }}
                                            @if($room->latestMessage)
                                                • {{ $room->latestMessage->created_at->diffForHumans() }}
                                            @endif
                                        </small>
                                        @if($room->latestMessage)
                                        <div class="small text-truncate mt-1">
                                            <strong>{{ $room->latestMessage->user->name }}:</strong> 
                                            {{ Str::limit($room->latestMessage->message, 30) }}
                                        </div>
                                        @endif
                                    </div>
                                    @php $unreadCount = $room->getUnreadCount(Auth::user()); @endphp
                                    @if($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Rooms publiques --}}
                    <div class="p-3">
                        <h6 class="text-muted mb-2">Rooms Publiques</h6>
                        <div id="publicRooms">
                            @foreach($publicRooms as $room)
                            <div class="room-item mb-2 p-2 rounded" data-room-id="{{ $room->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $room->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $room->creator->name }}
                                            • <i class="fas fa-users"></i> {{ $room->active_participants_count }}
                                        </small>
                                        @if($room->description)
                                        <div class="small text-muted mt-1">{{ Str::limit($room->description, 50) }}</div>
                                        @endif
                                    </div>
                                    @if(!$room->isParticipant(Auth::user()))
                                    <button class="btn btn-outline-primary btn-sm join-room-btn" data-room-id="{{ $room->id }}">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Zone principale --}}
        <div class="col-md-8 col-lg-9">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Bienvenue dans le Chat</h4>
                        <p class="text-muted">Sélectionnez une room pour commencer à discuter ou créez-en une nouvelle.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                            <i class="fas fa-plus"></i> Créer une Room
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Créer Room --}}
<div class="modal fade" id="createRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer une nouvelle room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createRoomForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roomName" class="form-label">Nom de la room *</label>
                        <input type="text" class="form-control" id="roomName" name="name" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="roomDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="roomDescription" name="description" rows="3" maxlength="500"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="roomType" class="form-label">Type de room</label>
                        <select class="form-select" id="roomType" name="type">
                            <option value="public">Publique (visible par tous)</option>
                            <option value="private">Privée (sur invitation)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="maxParticipants" class="form-label">Nombre maximum de participants</label>
                        <input type="number" class="form-control" id="maxParticipants" name="max_participants" value="50" min="2" max="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la room</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Rejoindre par code --}}
<div class="modal fade" id="joinByCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejoindre par code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="joinByCodeForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roomCode" class="form-label">Code de la room</label>
                        <input type="text" class="form-control text-uppercase" id="roomCode" name="room_code" required maxlength="8" placeholder="Ex: ABC12345">
                        <div class="form-text">Entrez le code à 8 caractères de la room</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Rejoindre</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.room-item {
    cursor: pointer;
    transition: background-color 0.2s;
    border: 1px solid transparent;
}

.room-item:hover {
    background-color: #f8f9fa !important;
    border-color: #dee2e6;
}

.room-item.active {
    background-color: #e3f2fd !important;
    border-color: #2196f3;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#searchRooms {
    border-right: none;
}

.btn-outline-secondary {
    border-left: none;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Chat Index chargé');

    // Éléments DOM
    const searchInput = document.getElementById('searchRooms');
    const createRoomForm = document.getElementById('createRoomForm');
    const joinByCodeForm = document.getElementById('joinByCodeForm');
    const roomCodeInput = document.getElementById('roomCode');

    // Recherche de rooms
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            loadPublicRooms();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchRooms(query);
        }, 300);
    });

    // Formatage automatique du code room
    roomCodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Gestion des clics sur les rooms
    document.addEventListener('click', function(e) {
        if (e.target.closest('.room-item')) {
            const roomItem = e.target.closest('.room-item');
            const roomId = roomItem.dataset.roomId;
            
            // Marquer comme active
            document.querySelectorAll('.room-item').forEach(item => {
                item.classList.remove('active');
            });
            roomItem.classList.add('active');
            
            // Rediriger vers la room
            window.location.href = `/chat/room/${roomId}`;
        }
        
        if (e.target.closest('.join-room-btn')) {
            e.stopPropagation();
            const btn = e.target.closest('.join-room-btn');
            const roomId = btn.dataset.roomId;
            joinRoom(roomId, btn);
        }
    });

    // Créer une room
    createRoomForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
        
        fetch('/chat/rooms', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Room créée avec succès!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('createRoomModal')).hide();
                this.reset();
                
                // Rediriger vers la nouvelle room
                setTimeout(() => {
                    window.location.href = `/chat/room/${data.room.id}`;
                }, 1000);
            } else {
                showAlert(data.message || 'Erreur lors de la création', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur de connexion', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Créer la room';
        });
    });

    // Rejoindre par code
    joinByCodeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
        
        fetch('/chat/rooms/join-by-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Room rejointe avec succès!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('joinByCodeModal')).hide();
                this.reset();
                location.reload();
            } else {
                showAlert(data.message || 'Code invalide', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur de connexion', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Rejoindre';
        });
    });

    // Fonctions utilitaires
    function searchRooms(query) {
        fetch(`/chat/rooms/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            updatePublicRooms(data.rooms);
        })
        .catch(error => {
            console.error('Erreur recherche:', error);
        });
    }

    function loadPublicRooms() {
        location.reload(); // Simple reload pour l'instant
    }

    function updatePublicRooms(rooms) {
        const container = document.getElementById('publicRooms');
        
        if (rooms.length === 0) {
            container.innerHTML = '<p class="text-muted small">Aucune room trouvée</p>';
            return;
        }
        
        container.innerHTML = rooms.map(room => `
            <div class="room-item mb-2 p-2 rounded" data-room-id="${room.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${room.name}</h6>
                        <small class="text-muted">
                            <i class="fas fa-user"></i> ${room.creator.name}
                            • <i class="fas fa-users"></i> ${room.active_participants_count}
                        </small>
                        ${room.description ? `<div class="small text-muted mt-1">${room.description.substring(0, 50)}${room.description.length > 50 ? '...' : ''}</div>` : ''}
                    </div>
                    <button class="btn btn-outline-primary btn-sm join-room-btn" data-room-id="${room.id}">
                        <i class="fas fa-sign-in-alt"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    function joinRoom(roomId, btn) {
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch(`/chat/rooms/${roomId}/join`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Room rejointe!', 'success');
                setTimeout(() => {
                    window.location.href = `/chat/room/${roomId}`;
                }, 1000);
            } else {
                showAlert(data.message || 'Impossible de rejoindre', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur de connexion', 'danger');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        });
    }

    function showAlert(message, type) {
        // Créer une alerte Bootstrap
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove après 5 secondes
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
