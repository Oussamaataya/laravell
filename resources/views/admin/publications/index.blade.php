@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Publications</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.publications.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Publication
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-1">Total</h6>
                                    <h3 class="mb-0">{{ \App\Models\Publication::count() }}</h3>
                                    <small class="text-muted">Total des publications</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-1">Approuvées</h6>
                                    <h3 class="mb-0 text-success">{{ \App\Models\Publication::where('is_approved', true)->count() }}</h3>
                                    <small class="text-muted">Publications approuvées</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-1">En attente</h6>
                                    <h3 class="mb-0 text-warning">{{ \App\Models\Publication::where('is_approved', false)->count() }}</h3>
                                    <small class="text-muted">Publications en attente</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-1">Commentaires</h6>
                                    <h3 class="mb-0">{{ \App\Models\Commentaire::count() }}</h3>
                                    <small class="text-muted">Total commentaires</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label class="form-label">Recherche</label>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Titre ou contenu">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Statut</label>
                                <select name="status" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvé</option>
                                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Auteur</label>
                                <select name="author" class="form-select">
                                    <option value="">Tous</option>
                                    @foreach($authors as $id => $name)
                                        <option value="{{ $id }}" {{ request('author') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">De</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="col-auto">
                                <label class="form-label">À</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="{{ route('admin.publications.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                            </div>
                        </div>
                    </form>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <div>
                            <input type="checkbox" id="select-all-publications"> <label for="select-all-publications">Tout sélectionner</label>
                        </div>
                        <div>
                            <select id="bulk-action-publications" class="form-select">
                                <option value="">Actions en masse</option>
                                <option value="approve">Approuver</option>
                                <option value="delete">Supprimer</option>
                            </select>
                        </div>
                        <div>
                            <button id="apply-bulk-publications" class="btn btn-outline-primary">Appliquer</button>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Date de création</th>
                                <th>Status</th>
                                <th>Commentaires</th>
                                <th>Likes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($publications as $publication)
                            <tr>
                                <td><input type="checkbox" class="pub-checkbox" value="{{ $publication->id }}"></td>
                                <td>{{ $publication->id }}</td>
                                <td>
                                    <a href="{{ route('admin.publications.show', $publication->id) }}?{{ http_build_query(request()->query()) }}">
                                        {{ $publication->titre }}
                                    </a>
                                </td>
                                <td>{{ $publication->user->name }}</td>
                                <td>{{ $publication->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.publications.approve', $publication->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $publication->is_approved ? 'btn-success' : 'btn-warning' }}">
                                            {{ $publication->is_approved ? 'Approuvé' : 'En attente' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $publication->commentaires->count() }}</td>
                                <td>{{ $publication->likes->count() }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="actions">
                                        <a href="{{ route('admin.publications.edit', $publication->id) }}?{{ http_build_query(request()->query()) }}" class="btn btn-sm btn-outline-info" title="Éditer">
                                                <i class="fas fa-edit me-1"></i> Éditer
                                            </a>
                                            <form action="{{ route('admin.publications.approve', $publication->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $publication->is_approved ? 'btn-outline-success' : 'btn-outline-warning' }}" title="Basculer statut">
                                                    <i class="fas fa-check-circle me-1"></i> {{ $publication->is_approved ? 'Désapprouver' : 'Approuver' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.publications.destroy', $publication->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')" title="Supprimer">
                                                    <i class="fas fa-trash me-1"></i> Supprimer
                                                </button>
                                            </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $publications->links('pagination::bootstrap-4') }}
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
    // Select all
    const selectAll = document.getElementById('select-all-publications');
    const checkboxes = document.querySelectorAll('.pub-checkbox');
    selectAll && selectAll.addEventListener('change', function(){
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('apply-bulk-publications').addEventListener('click', function(e){
        e.preventDefault();
        const action = document.getElementById('bulk-action-publications').value;
        const ids = Array.from(document.querySelectorAll('.pub-checkbox:checked')).map(i=>i.value);
        if(!action || ids.length===0){ alert('Sélectionnez une action et au moins une publication'); return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.publications.bulk') }}';
        form.style.display = 'none';
        const csrf = document.createElement('input'); csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
        const actionInput = document.createElement('input'); actionInput.name='action'; actionInput.value=action; form.appendChild(actionInput);
        ids.forEach(id=>{ const i = document.createElement('input'); i.name='ids[]'; i.value=id; form.appendChild(i); });
        document.body.appendChild(form); form.submit();
    });

    // Initialize Select2 on author select (if present)
    const authorSelect = document.querySelector('select[name="author"]');
    if(authorSelect){
        $(authorSelect).select2({
            placeholder: 'Sélectionner un auteur',
            allowClear: true,
            ajax: {
                url: '{{ route('admin.users.search') }}',
                dataType: 'json',
                delay: 250,
                data: function(params){ return { q: params.term }; },
                processResults: function(data){ return { results: data.results }; }
            }
        });
    }
});
</script>
@endpush