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
                    <li class="breadcrumb-item active">Éditer</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-warning me-2"></i>
                Éditer la Chat Room
            </h1>
            <p class="text-muted mb-0">Modifiez les paramètres de "{{ $chatRoom->name }}"</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Configuration de la room
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.chat-rooms.update', $chatRoom->id) }}" method="POST" id="editRoomForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="fas fa-tag me-1"></i>
                                        Nom de la room <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $chatRoom->name) }}" 
                                           placeholder="Ex: Discussion Générale"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Le nom sera visible par tous les utilisateurs</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-bold">
                                        <i class="fas fa-layer-group me-1"></i>
                                        Type de room <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="public" {{ old('type', $chatRoom->type) === 'public' ? 'selected' : '' }}>
                                            🔓 Publique
                                        </option>
                                        <option value="private" {{ old('type', $chatRoom->type) === 'private' ? 'selected' : '' }}>
                                            🔒 Privée
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">
                                <i class="fas fa-align-left me-1"></i>
                                Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Décrivez le sujet ou l'objectif de cette room...">{{ old('description', $chatRoom->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Description optionnelle visible dans la liste des rooms</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user-crown me-1"></i>
                                        Créateur/Administrateur
                                    </label>
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
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
                                    <div class="form-text">Le créateur ne peut pas être modifié après création</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_participants" class="form-label fw-bold">
                                        <i class="fas fa-users me-1"></i>
                                        Limite de participants
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('max_participants') is-invalid @enderror" 
                                           id="max_participants" 
                                           name="max_participants" 
                                           value="{{ old('max_participants', $chatRoom->max_participants) }}" 
                                           min="2" 
                                           max="1000"
                                           placeholder="Ex: 50">
                                    @error('max_participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Laissez vide pour aucune limite</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-key me-1"></i>
                                Code d'invitation
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $chatRoom->room_code }}" 
                                       readonly>
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        onclick="regenerateCode()"
                                        title="Régénérer le code">
                                    <i class="fas fa-sync"></i>
                                    Régénérer
                                </button>
                            </div>
                            <div class="form-text">Code unique permettant aux utilisateurs de rejoindre la room</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $chatRoom->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Room active
                                </label>
                                <div class="form-text">Les rooms inactives ne sont pas visibles par les utilisateurs</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.chat-rooms.show', $chatRoom->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Retour aux détails
                            </a>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.chat-rooms.index') }}" class="btn btn-outline-secondary">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>
                                    Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panneau d'informations -->
        <div class="col-lg-4">
            <!-- Statistiques actuelles -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistiques actuelles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stats-number text-primary">{{ $chatRoom->participants()->count() }}</div>
                            <div class="stats-label">Participants</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stats-number text-success">{{ $chatRoom->messages()->count() }}</div>
                            <div class="stats-label">Messages</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-number text-info">{{ $chatRoom->created_at->diffInDays() }}</div>
                            <div class="stats-label">Jours d'existence</div>
                        </div>
                        <div class="col-6">
                            <div class="stats-number text-warning">
                                {{ $chatRoom->last_activity ? $chatRoom->last_activity->diffInHours() : 'N/A' }}
                            </div>
                            <div class="stats-label">Heures depuis activité</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.chat-rooms.participants', $chatRoom->id) }}" class="btn btn-primary">
                            <i class="fas fa-users me-1"></i>
                            Gérer les participants ({{ $chatRoom->participants()->count() }})
                        </a>
                        
                        <button type="button" class="btn btn-info" onclick="exportMessages()">
                            <i class="fas fa-download me-1"></i>
                            Exporter les messages
                        </button>
                        
                        <form action="{{ route('admin.chat-rooms.toggle-status', $chatRoom->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $chatRoom->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $chatRoom->is_active ? 'pause' : 'play' }} me-1"></i>
                                {{ $chatRoom->is_active ? 'Désactiver' : 'Activer' }} la room
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Historique des modifications -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <strong>Créée le :</strong><br>
                            {{ $chatRoom->created_at->format('d/m/Y à H:i') }}
                            <span class="text-muted">({{ $chatRoom->created_at->diffForHumans() }})</span>
                        </div>
                        <div class="mb-2">
                            <strong>Dernière modification :</strong><br>
                            {{ $chatRoom->updated_at->format('d/m/Y à H:i') }}
                            <span class="text-muted">({{ $chatRoom->updated_at->diffForHumans() }})</span>
                        </div>
                        @if($chatRoom->last_activity)
                        <div class="mb-2">
                            <strong>Dernière activité :</strong><br>
                            {{ $chatRoom->last_activity->format('d/m/Y à H:i') }}
                            <span class="text-muted">({{ $chatRoom->last_activity->diffForHumans() }})</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Validation du formulaire
    const form = document.getElementById('editRoomForm');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const type = document.getElementById('type').value;

        if (!name) {
            e.preventDefault();
            alert('⚠️ Le nom de la room est obligatoire.');
            document.getElementById('name').focus();
            return;
        }

        if (!type) {
            e.preventDefault();
            alert('⚠️ Veuillez sélectionner un type de room.');
            document.getElementById('type').focus();
            return;
        }

        // Confirmation
        const confirmMessage = `Mettre à jour la room "${name}" ?`;
        if (!confirm(confirmMessage)) {
            e.preventDefault();
        }
    });

    // Fonction pour régénérer le code d'invitation
    window.regenerateCode = function() {
        if (confirm('Régénérer le code d\'invitation ?\n\nL\'ancien code ne fonctionnera plus.')) {
            fetch('{{ route("admin.chat-rooms.regenerate-code", $chatRoom->id) }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la régénération du code.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la régénération du code.');
            });
        }
    };

    // Fonction d'export des messages
    window.exportMessages = function() {
        const url = '{{ route("admin.chat-rooms.export-messages", $chatRoom->id) }}';
        window.open(url, '_blank');
    };

    // Prévisualisation des changements
    const nameInput = document.getElementById('name');
    const typeSelect = document.getElementById('type');
    const isActiveCheckbox = document.getElementById('is_active');

    function showPreview() {
        const changes = [];
        
        if (nameInput.value !== '{{ $chatRoom->name }}') {
            changes.push(`Nom: "${nameInput.value}"`);
        }
        
        if (typeSelect.value !== '{{ $chatRoom->type }}') {
            changes.push(`Type: ${typeSelect.value}`);
        }
        
        if (isActiveCheckbox.checked !== {{ $chatRoom->is_active ? 'true' : 'false' }}) {
            changes.push(`Statut: ${isActiveCheckbox.checked ? 'Active' : 'Inactive'}`);
        }

        if (changes.length > 0) {
            console.log('Modifications détectées:', changes);
        }
    }

    nameInput.addEventListener('input', showPreview);
    typeSelect.addEventListener('change', showPreview);
    isActiveCheckbox.addEventListener('change', showPreview);
});
</script>

<style>
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

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

.input-group .btn {
    border-left: 0;
}

.avatar-sm {
    flex-shrink: 0;
}
</style>
@endpush
