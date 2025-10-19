@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Gestion des Chat Rooms
                    </h1>
                    <p class="text-muted mb-0">Gérez tous les groupes de chat de la plateforme</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-info fs-6 px-3 py-2">
                        <i class="fas fa-comments me-1"></i>
                        {{ $chatRooms->total() }} rooms
                    </span>
                    <a href="{{ route('admin.chat-rooms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Nouvelle Room
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques détaillées -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card fade-in h-100">
                <div class="card-body text-center">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stats-number text-primary">{{ \App\Models\ChatRoom::count() }}</div>
                    <div class="stats-label">Total des rooms</div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>
                            +{{ \App\Models\ChatRoom::whereDate('created_at', today())->count() }} aujourd'hui
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card fade-in h-100" style="animation-delay: 0.1s;">
                <div class="card-body text-center">
                    <div class="stats-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number text-success">{{ \App\Models\ChatRoom::where('is_active', true)->count() }}</div>
                    <div class="stats-label">Rooms actives</div>
                    <div class="mt-2">
                        @php
                            $activePercentage = \App\Models\ChatRoom::count() > 0 
                                ? round((\App\Models\ChatRoom::where('is_active', true)->count() / \App\Models\ChatRoom::count()) * 100, 1)
                                : 0;
                        @endphp
                        <small class="text-success">
                            <i class="fas fa-percentage me-1"></i>
                            {{ $activePercentage }}% du total
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card fade-in h-100" style="animation-delay: 0.2s;">
                <div class="card-body text-center">
                    <div class="stats-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number text-info">{{ \App\Models\ChatParticipant::count() }}</div>
                    <div class="stats-label">Total participants</div>
                    <div class="mt-2">
                        @php
                            $avgParticipants = \App\Models\ChatRoom::count() > 0 
                                ? round(\App\Models\ChatParticipant::count() / \App\Models\ChatRoom::count(), 1)
                                : 0;
                        @endphp
                        <small class="text-info">
                            <i class="fas fa-chart-line me-1"></i>
                            {{ $avgParticipants }} par room
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stats-card fade-in h-100" style="animation-delay: 0.3s;">
                <div class="card-body text-center">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stats-number text-warning">{{ \App\Models\ChatMessage::count() }}</div>
                    <div class="stats-label">Messages envoyés</div>
                    <div class="mt-2">
                        <small class="text-warning">
                            <i class="fas fa-clock me-1"></i>
                            {{ \App\Models\ChatMessage::whereDate('created_at', today())->count() }} aujourd'hui
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtres de recherche
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-search me-1"></i>
                            Recherche globale
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" 
                                   placeholder="Nom ou description...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="fas fa-layer-group me-1"></i>
                            Type de room
                        </label>
                        <select name="type" class="form-select">
                            <option value="">🌐 Tous les types</option>
                            <option value="public" {{ request('type')=='public'?'selected':'' }}>
                                🔓 Publiques
                            </option>
                            <option value="private" {{ request('type')=='private'?'selected':'' }}>
                                🔒 Privées
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="fas fa-toggle-on me-1"></i>
                            Statut
                        </label>
                        <select name="status" class="form-select">
                            <option value="">📋 Tous les statuts</option>
                            <option value="active" {{ request('status')=='active'?'selected':'' }}>
                                ✅ Actives
                            </option>
                            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>
                                ❌ Inactives
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="fas fa-user-crown me-1"></i>
                            Créateur
                        </label>
                        <select name="creator" class="form-select select2-creator">
                            <option value="">👥 Tous les créateurs</option>
                            @foreach($creators as $id => $name)
                                <option value="{{ $id }}" {{ request('creator') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="fas fa-sort-amount-down me-1"></i>
                            Trier par
                        </label>
                        <select name="sort" class="form-select">
                            <option value="created_at_desc" {{ request('sort', 'created_at_desc')=='created_at_desc'?'selected':'' }}>
                                📅 Plus récentes
                            </option>
                            <option value="created_at_asc" {{ request('sort')=='created_at_asc'?'selected':'' }}>
                                📅 Plus anciennes
                            </option>
                            <option value="name_asc" {{ request('sort')=='name_asc'?'selected':'' }}>
                                🔤 Nom A-Z
                            </option>
                            <option value="participants_desc" {{ request('sort')=='participants_desc'?'selected':'' }}>
                                👥 Plus de participants
                            </option>
                            <option value="messages_desc" {{ request('sort')=='messages_desc'?'selected':'' }}>
                                💬 Plus de messages
                            </option>
                            <option value="activity_desc" {{ request('sort')=='activity_desc'?'selected':'' }}>
                                ⚡ Plus actives
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Date de début
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Date de fin
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>
                                Appliquer les filtres
                            </button>
                            <a href="{{ route('admin.chat-rooms.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>
                                Réinitialiser
                            </a>
                            <button type="button" class="btn btn-outline-info" onclick="exportResults()">
                                <i class="fas fa-download me-1"></i>
                                Exporter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions en masse et tableau -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Liste des chat rooms</h5>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check">
                            <input type="checkbox" id="select-all-rooms" class="form-check-input">
                            <label for="select-all-rooms" class="form-check-label small">Tout sélectionner</label>
                        </div>
                        <select id="bulk-action-rooms" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Actions en masse</option>
                            <option value="activate">✅ Activer sélectionnées</option>
                            <option value="deactivate">❌ Désactiver sélectionnées</option>
                            <option value="delete">🗑️ Supprimer sélectionnées</option>
                        </select>
                        <button id="apply-bulk-rooms" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-play me-1"></i>
                            Appliquer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <div class="form-check">
                                    <input type="checkbox" id="select-all-rooms-table" class="form-check-input">
                                </div>
                            </th>
                            <th width="80">
                                <i class="fas fa-hashtag me-1"></i>
                                ID
                            </th>
                            <th>
                                <i class="fas fa-comments me-1"></i>
                                Room
                            </th>
                            <th width="150">
                                <i class="fas fa-user-crown me-1"></i>
                                Créateur
                            </th>
                            <th width="100">
                                <i class="fas fa-layer-group me-1"></i>
                                Type
                            </th>
                            <th width="100">
                                <i class="fas fa-toggle-on me-1"></i>
                                Statut
                            </th>
                            <th width="120" class="text-center">
                                <i class="fas fa-chart-bar me-1"></i>
                                Stats
                            </th>
                            <th width="130">
                                <i class="fas fa-calendar me-1"></i>
                                Créée le
                            </th>
                            <th width="200" class="text-center">
                                <i class="fas fa-cogs me-1"></i>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chatRooms as $room)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input room-checkbox" value="{{ $room->id }}">
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $room->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="me-3 flex-shrink-0">
                                        <div class="bg-{{ $room->type === 'public' ? 'primary' : 'warning' }} text-white rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-{{ $room->type === 'public' ? 'globe' : 'lock' }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.chat-rooms.show', $room->id) }}" 
                                               class="text-decoration-none fw-bold" 
                                               title="{{ $room->name }}">
                                                {{ Str::limit($room->name, 40) }}
                                            </a>
                                        </h6>
                                        @if($room->description)
                                            <p class="text-muted small mb-1">
                                                {{ Str::limit($room->description, 60) }}
                                            </p>
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge bg-secondary me-1 small">
                                                <i class="fas fa-key me-1"></i>
                                                {{ $room->room_code }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($room->creator->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $room->creator->name }}</div>
                                        <small class="text-muted">{{ $room->creator->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $room->type === 'public' ? 'primary' : 'warning' }}">
                                    <i class="fas fa-{{ $room->type === 'public' ? 'globe' : 'lock' }} me-1"></i>
                                    {{ ucfirst($room->type) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.chat-rooms.toggle-status', $room->id) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Changer le statut de cette room ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-sm {{ $room->is_active ? 'btn-success' : 'btn-secondary' }} w-100">
                                        @if($room->is_active)
                                            <i class="fas fa-check-circle me-1"></i>
                                            Active
                                        @else
                                            <i class="fas fa-pause-circle me-1"></i>
                                            Inactive
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <div class="text-center">
                                        <div class="fw-bold text-primary">{{ $room->participants_count }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i>
                                        </small>
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold text-info">{{ $room->messages_count }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-comments"></i>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-medium">{{ $room->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $room->created_at->format('H:i') }}</small>
                                    <br>
                                    <small class="text-info">{{ $room->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.chat-rooms.show', $room->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye me-1"></i> Voir
                                    </a>
                                    <a href="{{ route('admin.chat-rooms.participants', $room->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Gérer les participants">
                                        <i class="fas fa-users me-1"></i> Participants
                                    </a>
                                    <a href="{{ route('admin.chat-rooms.edit', $room->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Éditer">
                                        <i class="fas fa-edit me-1"></i> Éditer
                                    </a>
                                    <form action="{{ route('admin.chat-rooms.destroy', $room->id) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette room et tous ses messages ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <h5>Aucune chat room trouvée</h5>
                                    <p>Aucune room ne correspond à vos critères de recherche.</p>
                                    <a href="{{ route('admin.chat-rooms.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>
                                        Créer une nouvelle room
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($chatRooms->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Affichage de {{ $chatRooms->firstItem() }} à {{ $chatRooms->lastItem() }} 
                        sur {{ $chatRooms->total() }} chat rooms
                    </div>
                    <div>
                        {{ $chatRooms->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Gestion de la sélection multiple
    const selectAllHeader = document.getElementById('select-all-rooms');
    const selectAllTable = document.getElementById('select-all-rooms-table');
    const checkboxes = document.querySelectorAll('.room-checkbox');
    
    // Synchroniser les deux checkboxes "Tout sélectionner"
    function syncSelectAll() {
        const checkedCount = document.querySelectorAll('.room-checkbox:checked').length;
        const totalCount = checkboxes.length;
        
        if (selectAllHeader) {
            selectAllHeader.checked = checkedCount === totalCount;
            selectAllHeader.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }
        
        if (selectAllTable) {
            selectAllTable.checked = checkedCount === totalCount;
            selectAllTable.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }
        
        // Mettre à jour le texte du bouton d'action
        const applyBtn = document.getElementById('apply-bulk-rooms');
        if (applyBtn) {
            if (checkedCount > 0) {
                applyBtn.innerHTML = `<i class="fas fa-play me-1"></i>Appliquer (${checkedCount})`;
                applyBtn.classList.remove('btn-outline-primary');
                applyBtn.classList.add('btn-primary');
            } else {
                applyBtn.innerHTML = '<i class="fas fa-play me-1"></i>Appliquer';
                applyBtn.classList.remove('btn-primary');
                applyBtn.classList.add('btn-outline-primary');
            }
        }
    }

    // Événements pour les checkboxes "Tout sélectionner"
    [selectAllHeader, selectAllTable].forEach(selectAll => {
        if (selectAll) {
            selectAll.addEventListener('change', function(){
                checkboxes.forEach(cb => cb.checked = this.checked);
                syncSelectAll();
            });
        }
    });

    // Événements pour les checkboxes individuelles
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', syncSelectAll);
    });

    // Gestion des actions en masse
    const applyBulkBtn = document.getElementById('apply-bulk-rooms');
    if (applyBulkBtn) {
        applyBulkBtn.addEventListener('click', function(e){
            e.preventDefault();
            const action = document.getElementById('bulk-action-rooms').value;
            const ids = Array.from(document.querySelectorAll('.room-checkbox:checked')).map(cb => cb.value);
            
            if (!action) {
                alert('⚠️ Veuillez sélectionner une action à effectuer.');
                return;
            }
            
            if (ids.length === 0) {
                alert('⚠️ Veuillez sélectionner au moins une room.');
                return;
            }

            let confirmMessage = '';
            switch(action) {
                case 'activate':
                    confirmMessage = `✅ Activer ${ids.length} room(s) sélectionnée(s) ?`;
                    break;
                case 'deactivate':
                    confirmMessage = `❌ Désactiver ${ids.length} room(s) sélectionnée(s) ?`;
                    break;
                case 'delete':
                    confirmMessage = `🗑️ Êtes-vous sûr de vouloir supprimer ${ids.length} room(s) sélectionnée(s) ?\n\nCette action supprimera aussi tous les messages et participants associés et est irréversible.`;
                    break;
                default:
                    confirmMessage = `Confirmer l'action sur ${ids.length} room(s) ?`;
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            // Créer et soumettre le formulaire
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.chat-rooms.bulk") }}';
            form.style.display = 'none';
            
            // Token CSRF
            const csrf = document.createElement('input');
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            // Action
            const actionInput = document.createElement('input');
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            
            // IDs
            ids.forEach(id => {
                const idInput = document.createElement('input');
                idInput.name = 'ids[]';
                idInput.value = id;
                form.appendChild(idInput);
            });
            
            document.body.appendChild(form);
            form.submit();
        });
    }

    // Initialiser Select2 pour la sélection de créateur
    if (typeof $ !== 'undefined' && $('.select2-creator').length) {
        $('.select2-creator').select2({
            placeholder: '🔍 Rechercher un créateur...',
            allowClear: true,
            width: '100%'
        });
    }

    // Fonction d'export des résultats
    window.exportResults = function() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'csv');
        window.open(window.location.pathname + '?' + params.toString(), '_blank');
    };

    // Initialiser la synchronisation
    syncSelectAll();
});
</script>
@endpush
