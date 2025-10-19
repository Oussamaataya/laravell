@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Gestion des Commentaires
                    </h1>
                    <p class="text-muted mb-0">Modérez et gérez tous les commentaires de la plateforme</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-info fs-6 px-3 py-2">
                        <i class="fas fa-comment me-1"></i>
                        {{ $commentaires->total() }} commentaires
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card fade-in">
                <div class="card-body text-center">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stats-number text-primary">{{ \App\Models\Commentaire::count() }}</div>
                    <div class="stats-label">Total commentaires</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card fade-in" style="animation-delay: 0.1s;">
                <div class="card-body text-center">
                    <div class="stats-icon text-success">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number text-success">{{ \App\Models\Commentaire::whereDate('created_at', today())->count() }}</div>
                    <div class="stats-label">Aujourd'hui</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card fade-in" style="animation-delay: 0.2s;">
                <div class="card-body text-center">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stats-number text-warning">{{ \App\Models\Commentaire::where('created_at', '>=', now()->subWeek())->count() }}</div>
                    <div class="stats-label">Cette semaine</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card fade-in" style="animation-delay: 0.3s;">
                <div class="card-body text-center">
                    <div class="stats-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number text-info">{{ \App\Models\Commentaire::distinct('user_id')->count() }}</div>
                    <div class="stats-label">Auteurs uniques</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres améliorés -->
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
                            Recherche dans le contenu
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" 
                                   placeholder="Tapez pour rechercher...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-user me-1"></i>
                            Auteur du commentaire
                        </label>
                        <select name="author" class="form-select select2-author">
                            <option value="">🔍 Tous les auteurs</option>
                            @foreach($authors as $id => $name)
                                <option value="{{ $id }}" {{ request('author') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">
                            <i class="fas fa-newspaper me-1"></i>
                            Publication associée
                        </label>
                        <select name="publication" class="form-select">
                            <option value="">📰 Toutes les publications</option>
                            @foreach($publications as $id => $titre)
                                <option value="{{ $id }}" {{ request('publication') == $id ? 'selected' : '' }}>
                                    {{ Str::limit($titre, 50) }}
                                </option>
                            @endforeach
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
                            <a href="{{ route('admin.commentaires.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>
                                Réinitialiser
                            </a>
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
                    <h5 class="mb-0">Liste des commentaires</h5>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check">
                            <input type="checkbox" id="select-all-comments" class="form-check-input">
                            <label for="select-all-comments" class="form-check-label small">Tout sélectionner</label>
                        </div>
                        <select id="bulk-action-comments" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Actions en masse</option>
                            <option value="delete">🗑️ Supprimer sélectionnés</option>
                        </select>
                        <button id="apply-bulk-comments" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-play me-1"></i>
                            Appliquer
                        </button>
                    </div>
                </div>
            </div>
        </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <div class="form-check">
                                    <input type="checkbox" id="select-all-comments-table" class="form-check-input">
                                </div>
                            </th>
                            <th width="80">
                                <i class="fas fa-hashtag me-1"></i>
                                ID
                            </th>
                            <th>
                                <i class="fas fa-newspaper me-1"></i>
                                Publication
                            </th>
                            <th width="150">
                                <i class="fas fa-user me-1"></i>
                                Auteur
                            </th>
                            <th>
                                <i class="fas fa-comment me-1"></i>
                                Commentaire
                            </th>
                            <th width="130">
                                <i class="fas fa-calendar me-1"></i>
                                Date
                            </th>
                            <th width="120" class="text-center">
                                <i class="fas fa-cogs me-1"></i>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commentaires as $commentaire)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input comment-checkbox" value="{{ $commentaire->id }}">
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $commentaire->id }}</span>
                            </td>
                            <td>
                                @if($commentaire->publication)
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-newspaper text-primary"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('publications.show', $commentaire->publication->id) }}" 
                                               class="text-decoration-none fw-medium" 
                                               target="_blank" 
                                               title="{{ $commentaire->publication->titre }}">
                                                {{ Str::limit($commentaire->publication->titre, 40) }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                Par {{ $commentaire->publication->user->name ?? 'Inconnu' }}
                                            </small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Publication supprimée
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($commentaire->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($commentaire->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $commentaire->user->name }}</div>
                                            <small class="text-muted">{{ $commentaire->user->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-user-slash me-1"></i>
                                        Utilisateur supprimé
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="comment-content">
                                    <p class="mb-1">{{ Str::limit($commentaire->contenu, 100) }}</p>
                                    @if(strlen($commentaire->contenu) > 100)
                                        <button class="btn btn-link btn-sm p-0 text-primary" 
                                                onclick="toggleFullComment({{ $commentaire->id }})">
                                            <small>Voir plus...</small>
                                        </button>
                                        <div id="full-comment-{{ $commentaire->id }}" class="d-none">
                                            <p class="mb-1">{{ $commentaire->contenu }}</p>
                                            <button class="btn btn-link btn-sm p-0 text-primary" 
                                                    onclick="toggleFullComment({{ $commentaire->id }})">
                                                <small>Voir moins</small>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-medium">{{ $commentaire->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $commentaire->created_at->format('H:i') }}</small>
                                    <br>
                                    <small class="text-info">{{ $commentaire->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @if($commentaire->publication)
                                        <a href="{{ route('publications.show', $commentaire->publication->id) }}#comment-{{ $commentaire->id }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           target="_blank" 
                                           title="Voir le commentaire">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.commentaires.destroy', $commentaire->id) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <h5>Aucun commentaire trouvé</h5>
                                    <p>Aucun commentaire ne correspond à vos critères de recherche.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($commentaires->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Affichage de {{ $commentaires->firstItem() }} à {{ $commentaires->lastItem() }} 
                        sur {{ $commentaires->total() }} commentaires
                    </div>
                    <div>
                        {{ $commentaires->appends(request()->query())->links('pagination::bootstrap-4') }}
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

<style>
.comment-content p {
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.avatar-sm {
    flex-shrink: 0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Fonction pour basculer l'affichage complet des commentaires
    window.toggleFullComment = function(commentId) {
        const shortContent = document.querySelector(`#comment-${commentId} .comment-content p:first-child`);
        const fullContent = document.querySelector(`#full-comment-${commentId}`);
        
        if (fullContent.classList.contains('d-none')) {
            shortContent.style.display = 'none';
            fullContent.classList.remove('d-none');
        } else {
            shortContent.style.display = 'block';
            fullContent.classList.add('d-none');
        }
    };

    // Gestion de la sélection multiple
    const selectAllHeader = document.getElementById('select-all-comments');
    const selectAllTable = document.getElementById('select-all-comments-table');
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    
    // Synchroniser les deux checkboxes "Tout sélectionner"
    function syncSelectAll() {
        const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
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
        const applyBtn = document.getElementById('apply-bulk-comments');
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
    const applyBulkBtn = document.getElementById('apply-bulk-comments');
    if (applyBulkBtn) {
        applyBulkBtn.addEventListener('click', function(e){
            e.preventDefault();
            const action = document.getElementById('bulk-action-comments').value;
            const ids = Array.from(document.querySelectorAll('.comment-checkbox:checked')).map(cb => cb.value);
            
            if (!action) {
                alert('⚠️ Veuillez sélectionner une action à effectuer.');
                return;
            }
            
            if (ids.length === 0) {
                alert('⚠️ Veuillez sélectionner au moins un commentaire.');
                return;
            }

            let confirmMessage = '';
            switch(action) {
                case 'delete':
                    confirmMessage = `🗑️ Êtes-vous sûr de vouloir supprimer ${ids.length} commentaire(s) sélectionné(s) ?\n\nCette action est irréversible.`;
                    break;
                default:
                    confirmMessage = `Confirmer l'action sur ${ids.length} commentaire(s) ?`;
            }

            if (!confirm(confirmMessage)) {
                return;
            }

            // Créer et soumettre le formulaire
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.commentaires.bulk") }}';
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

    // Initialiser Select2 pour la sélection d'auteur
    if (typeof $ !== 'undefined' && $('.select2-author').length) {
        $('.select2-author').select2({
            placeholder: '🔍 Rechercher un auteur...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("admin.users.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(user) {
                if (user.loading) {
                    return user.text;
                }
                return $('<span><i class="fas fa-user me-2"></i>' + user.text + '</span>');
            },
            templateSelection: function(user) {
                return user.text || user.id;
            }
        });
    }

    // Initialisation des tooltips Bootstrap si disponible
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialiser la synchronisation
    syncSelectAll();
});
</script>
@endpush
