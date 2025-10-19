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
                    <li class="breadcrumb-item active">Nouvelle Room</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus text-primary me-2"></i>
                Créer une nouvelle Chat Room
            </h1>
            <p class="text-muted mb-0">Configurez les paramètres de la nouvelle room de discussion</p>
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
                    <form action="{{ route('admin.chat-rooms.store') }}" method="POST" id="createRoomForm">
                        @csrf
                        
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
                                           value="{{ old('name') }}" 
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
                                        <option value="public" {{ old('type') === 'public' ? 'selected' : '' }}>
                                            🔓 Publique
                                        </option>
                                        <option value="private" {{ old('type') === 'private' ? 'selected' : '' }}>
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
                                      placeholder="Décrivez le sujet ou l'objectif de cette room...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Description optionnelle visible dans la liste des rooms</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="created_by" class="form-label fw-bold">
                                        <i class="fas fa-user-crown me-1"></i>
                                        Créateur/Administrateur <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select select2-users @error('created_by') is-invalid @enderror" 
                                            id="created_by" 
                                            name="created_by" 
                                            required>
                                        <option value="">Sélectionner un utilisateur</option>
                                        @foreach($users as $id => $name)
                                            <option value="{{ $id }}" {{ old('created_by') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('created_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Cet utilisateur sera automatiquement administrateur de la room</div>
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
                                           value="{{ old('max_participants') }}" 
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

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Room active
                                </label>
                                <div class="form-text">Les rooms inactives ne sont pas visibles par les utilisateurs</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.chat-rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Retour à la liste
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Créer la room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panneau d'aide -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Aide à la création
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="fas fa-globe me-1"></i>
                            Rooms Publiques
                        </h6>
                        <p class="small text-muted mb-0">
                            Visibles par tous les utilisateurs dans la liste des rooms disponibles. 
                            Tout utilisateur peut rejoindre une room publique.
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-warning">
                            <i class="fas fa-lock me-1"></i>
                            Rooms Privées
                        </h6>
                        <p class="small text-muted mb-0">
                            Accessibles uniquement par invitation via le code d'invitation. 
                            Non visibles dans la liste publique.
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-info">
                            <i class="fas fa-key me-1"></i>
                            Code d'invitation
                        </h6>
                        <p class="small text-muted mb-0">
                            Un code unique sera généré automatiquement pour permettre 
                            aux utilisateurs de rejoindre la room.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Prévisualisation -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Prévisualisation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="preview-room-card border rounded p-3">
                        <div class="d-flex align-items-start">
                            <div class="me-3 flex-shrink-0">
                                <div id="preview-icon" class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-globe"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <span id="preview-name">Nom de la room</span>
                                </h6>
                                <p class="text-muted small mb-1" id="preview-description">
                                    Description de la room...
                                </p>
                                <div class="mt-1">
                                    <span class="badge bg-secondary me-1 small">
                                        <i class="fas fa-key me-1"></i>
                                        ABC123
                                    </span>
                                    <span id="preview-type" class="badge bg-primary small">
                                        <i class="fas fa-globe me-1"></i>
                                        Public
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-text mt-2">
                        Aperçu de l'affichage de votre room dans la liste
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

    // Prévisualisation en temps réel
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const typeSelect = document.getElementById('type');
    
    const previewName = document.getElementById('preview-name');
    const previewDescription = document.getElementById('preview-description');
    const previewType = document.getElementById('preview-type');
    const previewIcon = document.getElementById('preview-icon');

    function updatePreview() {
        // Nom
        const name = nameInput.value || 'Nom de la room';
        previewName.textContent = name;

        // Description
        const description = descriptionInput.value || 'Description de la room...';
        previewDescription.textContent = description.length > 60 ? description.substring(0, 60) + '...' : description;

        // Type
        const type = typeSelect.value;
        if (type === 'public') {
            previewType.innerHTML = '<i class="fas fa-globe me-1"></i>Public';
            previewType.className = 'badge bg-primary small';
            previewIcon.innerHTML = '<i class="fas fa-globe"></i>';
            previewIcon.className = 'bg-primary text-white rounded d-flex align-items-center justify-content-center';
        } else if (type === 'private') {
            previewType.innerHTML = '<i class="fas fa-lock me-1"></i>Privé';
            previewType.className = 'badge bg-warning small';
            previewIcon.innerHTML = '<i class="fas fa-lock"></i>';
            previewIcon.className = 'bg-warning text-white rounded d-flex align-items-center justify-content-center';
        } else {
            previewType.innerHTML = '<i class="fas fa-question me-1"></i>Type';
            previewType.className = 'badge bg-secondary small';
            previewIcon.innerHTML = '<i class="fas fa-question"></i>';
            previewIcon.className = 'bg-secondary text-white rounded d-flex align-items-center justify-content-center';
        }
    }

    // Écouter les changements
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', updatePreview);

    // Validation du formulaire
    const form = document.getElementById('createRoomForm');
    form.addEventListener('submit', function(e) {
        const name = nameInput.value.trim();
        const type = typeSelect.value;
        const creatorId = document.getElementById('created_by').value;

        if (!name) {
            e.preventDefault();
            alert('⚠️ Le nom de la room est obligatoire.');
            nameInput.focus();
            return;
        }

        if (!type) {
            e.preventDefault();
            alert('⚠️ Veuillez sélectionner un type de room.');
            typeSelect.focus();
            return;
        }

        if (!creatorId) {
            e.preventDefault();
            alert('⚠️ Veuillez sélectionner un créateur pour la room.');
            document.getElementById('created_by').focus();
            return;
        }

        // Confirmation
        const confirmMessage = `Créer la room "${name}" de type ${type} ?`;
        if (!confirm(confirmMessage)) {
            e.preventDefault();
        }
    });

    // Initialiser la prévisualisation
    updatePreview();
});
</script>

<style>
.preview-room-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
}

.preview-room-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}
</style>
@endpush
