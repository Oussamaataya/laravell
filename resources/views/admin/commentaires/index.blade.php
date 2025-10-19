@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Commentaires</h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label class="form-label">Recherche</label>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Contenu du commentaire">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Auteur</label>
                                <select name="author" class="form-select select2-author">
                                    <option value="">Tous</option>
                                    @foreach($authors as $id => $name)
                                        <option value="{{ $id }}" {{ request('author') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Publication</label>
                                <select name="publication" class="form-select">
                                    <option value="">Toutes</option>
                                    @foreach($publications as $id => $titre)
                                        <option value="{{ $id }}" {{ request('publication') == $id ? 'selected' : '' }}>{{ $titre }}</option>
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
                                <a href="{{ route('admin.commentaires.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                            </div>
                        </div>
                    </form>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <div>
                            <input type="checkbox" id="select-all-comments"> <label for="select-all-comments">Tout sélectionner</label>
                        </div>
                        <div>
                            <select id="bulk-action-comments" class="form-select">
                                <option value="">Actions en masse</option>
                                <option value="delete">Supprimer</option>
                            </select>
                        </div>
                        <div>
                            <button id="apply-bulk-comments" class="btn btn-outline-primary">Appliquer</button>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Publication</th>
                                <th>Auteur</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commentaires as $commentaire)
                            <tr>
                                <td><input type="checkbox" class="comment-checkbox" value="{{ $commentaire->id }}"></td>
                                <td>{{ $commentaire->id }}</td>
                                <td>{{ $commentaire->publication->titre ?? '-' }}</td>
                                <td>{{ $commentaire->user->name ?? '-' }}</td>
                                <td>{{ $commentaire->contenu }}</td>
                                <td>{{ $commentaire->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.commentaires.destroy', $commentaire->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce commentaire ?')" title="Supprimer">
                                            <i class="fas fa-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $commentaires->links('pagination::bootstrap-4') }}
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
    // Select all comments
    const selectAll = document.getElementById('select-all-comments');
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    selectAll && selectAll.addEventListener('change', function(){
        document.querySelectorAll('.comment-checkbox').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('apply-bulk-comments').addEventListener('click', function(e){
        e.preventDefault();
        const action = document.getElementById('bulk-action-comments').value;
        const ids = Array.from(document.querySelectorAll('.comment-checkbox:checked')).map(i=>i.value);
        if(!action || ids.length===0){ alert('Sélectionnez une action et au moins un commentaire'); return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.commentaires.bulk') }}';
        form.style.display = 'none';
        const csrf = document.createElement('input'); csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
        const actionInput = document.createElement('input'); actionInput.name='action'; actionInput.value=action; form.appendChild(actionInput);
        ids.forEach(id=>{ const i = document.createElement('input'); i.name='ids[]'; i.value=id; form.appendChild(i); });
        document.body.appendChild(form); form.submit();
    });

    // Initialize Select2 for author select
    $('.select2-author').select2({
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
});
</script>
@endpush
