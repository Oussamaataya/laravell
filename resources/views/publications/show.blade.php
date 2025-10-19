@extends('layouts.app')

@section('title', $publication->titre)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('publications.index') }}">Publications</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $publication->titre }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="card-title h2">{{ $publication->titre }}</h1>
                <div>
                    @auth
                        @if(auth()->user()->id === $publication->user_id || auth()->user()->role === 'admin')
                            <a href="{{ route('publications.edit', $publication) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('publications.destroy', $publication) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($publication->user->name) }}&background=random" class="rounded-circle" width="40" height="40" alt="{{ $publication->user->name }}">
                </div>
                <div>
                    <div class="fw-bold">{{ $publication->user->name }}</div>
                    <div class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i> Publié le {{ $publication->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>

            @if($publication->image)
                @php
                    // Use dedicated route to serve images via Storage disk 'public' to avoid webserver permission issues
                    $filename = basename($publication->image);
                    $imgSrc = route('publications.image', ['filename' => $filename]);
                @endphp
                <div class="text-center my-4">
                    <img src="{{ $imgSrc }}" class="img-fluid rounded" alt="{{ $publication->titre }}" style="max-height: 400px;">
                </div>
            @endif

            <div class="my-4">
                <p class="lead">{{ $publication->contenu }}</p>

                @if(!empty($publication->ai_description))
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Suggestion (IA) :</strong>
                        <div class="small text-muted">{{ $publication->ai_description }}</div>
                    </div>
                @endif

                @if(!empty($publication->ai_hashtags) && is_array($publication->ai_hashtags))
                    <div class="mt-2">
                        @foreach($publication->ai_hashtags as $tag)
                            <a href="/search?tag={{ urlencode($tag) }}" class="badge bg-success text-decoration-none me-1">{{ $tag }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center border-top border-bottom py-3 my-4">
                <div>
                    <span class="me-3">
                        <i class="fas fa-comment text-muted"></i> {{ $publication->commentaires->count() }} commentaires
                    </span>
                </div>
                <div>
                    @auth
                        <form action="{{ route('publications.like', $publication) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $publication->likedBy(auth()->user()) ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-heart"></i> 
                                {{ $publication->likes->count() }} J'aime
                            </button>
                        </form>
                    @else
                        <span class="text-muted">
                            <i class="fas fa-heart"></i> {{ $publication->likes->count() }} J'aime
                        </span>
                    @endauth
                </div>
            </div>

            <!-- Section commentaires -->
            <div class="mt-5">
                <h3 class="h4 mb-4">Commentaires ({{ $publication->commentaires->count() }})</h3>
                
                @auth
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="{{ route('commentaires.store', $publication) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="contenu" class="form-label">Ajouter un commentaire</label>
                                    <textarea class="form-control @error('contenu') is-invalid @enderror" id="contenu" name="contenu" rows="3" required>{{ old('contenu') }}</textarea>
                                    {{-- Client-side bad-word alert (hidden by default) --}}
                                    <div id="badword-alert" class="alert alert-warning mt-2 d-none" role="alert" style="display:none;">
                                        <strong>Attention :</strong> Votre commentaire contient des mots inappropriés et ne peut pas être publié.
                                        <div id="badword-list" class="small mt-1"></div>
                                    </div>
                                    @error('contenu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" id="submit-comment-btn" class="btn btn-primary">Publier</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <a href="{{ route('login') }}">Connectez-vous</a> pour ajouter un commentaire.
                    </div>
                @endauth

                <div class="commentaires-list">
                    @forelse($publication->commentaires->sortByDesc('created_at') as $commentaire)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($commentaire->user->name) }}&background=random" class="rounded-circle me-2" width="30" height="30" alt="{{ $commentaire->user->name }}">
                                        <span class="fw-bold">{{ $commentaire->user->name }}</span>
                                    </div>
                                    <small class="text-muted">{{ $commentaire->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="card-text">{{ $commentaire->contenu }}</p>
                                
                                @auth
                                    @if(auth()->user()->id === $commentaire->user_id || auth()->user()->role === 'admin')
                                        <div class="d-flex justify-content-end mt-2">
                                            <button class="btn btn-sm btn-link text-decoration-none edit-comment-btn" data-id="{{ $commentaire->id }}" data-content="{{ $commentaire->contenu }}">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            <form action="{{ route('commentaires.destroy', $commentaire) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="edit-comment-form d-none mt-3" id="edit-form-{{ $commentaire->id }}">
                                            <form action="{{ route('commentaires.update', $commentaire) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <textarea class="form-control" name="contenu" rows="2" required>{{ $commentaire->contenu }}</textarea>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 cancel-edit-btn">Annuler</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light">
                            Aucun commentaire pour le moment. Soyez le premier à commenter!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'édition des commentaires
        const editBtns = document.querySelectorAll('.edit-comment-btn');
        const cancelBtns = document.querySelectorAll('.cancel-edit-btn');
        
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const commentId = this.dataset.id;
                const formElement = document.getElementById(`edit-form-${commentId}`);
                formElement.classList.remove('d-none');
                this.closest('.card-body').querySelector('.card-text').classList.add('d-none');
                this.closest('.d-flex').classList.add('d-none');
            });
        });
        
        cancelBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const formElement = this.closest('.edit-comment-form');
                formElement.classList.add('d-none');
                const cardBody = formElement.closest('.card-body');
                cardBody.querySelector('.card-text').classList.remove('d-none');
                cardBody.querySelector('.d-flex.justify-content-end').classList.remove('d-none');
            });
        });
    });
</script>
<script>
    // Intelligent moderation with real-time feedback
    (function(){
        const textarea = document.getElementById('contenu');
        const alertBox = document.getElementById('badword-alert');
        const badwordList = document.getElementById('badword-list');
        const submitBtn = document.getElementById('submit-comment-btn');
        
        // Add progress indicator
        const progressContainer = document.createElement('div');
        progressContainer.className = 'mt-2';
        progressContainer.innerHTML = `
            <div class="progress" style="height: 4px; display: none;" id="moderation-progress">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <small class="text-muted" id="moderation-status" style="display: none;"></small>
        `;
        textarea.parentNode.insertBefore(progressContainer, alertBox);
        
        const progressBar = document.getElementById('moderation-progress');
        const progressBarFill = progressBar.querySelector('.progress-bar');
        const statusText = document.getElementById('moderation-status');

        if (!textarea) return;

        let analysisTimeout;
        let lastAnalyzedText = '';

        function analyzeContent(text) {
            if (text === lastAnalyzedText || text.trim().length < 3) {
                return;
            }
            
            lastAnalyzedText = text;
            
            // Show progress
            progressBar.style.display = 'block';
            statusText.style.display = 'block';
            statusText.textContent = 'Analyse en cours...';
            progressBarFill.style.width = '30%';
            
            // Simulate analysis (replace with actual AJAX call)
            setTimeout(() => {
                const analysis = performIntelligentAnalysis(text);
                updateModerationFeedback(analysis);
            }, 500);
        }

        function performIntelligentAnalysis(text) {
            // Enhanced client-side analysis with pattern detection
            const patterns = {
                profanity: {
                    words: ['merde','salaud','connard','con','putain','fdp','enculé','salope'],
                    variants: ['m3rd3', 'c0nn4rd', 'put41n', 's4l4ud'],
                    severity: 70
                },
                hate: {
                    words: ['ta mère', 'nique', 'crève'],
                    variants: ['t4 m3r3', 'n1qu3'],
                    severity: 85
                },
                aggressive: {
                    patterns: [/[!]{3,}/, /[A-Z]{5,}/, /va\s*te\s*faire/i],
                    severity: 50
                }
            };
            
            let score = 0;
            let detectedWords = [];
            let categories = [];
            
            const normalizedText = text.toLowerCase()
                .replace(/[^a-z0-9\s]/g, ' ')
                .replace(/\s+/g, ' ');
            
            // Check profanity and variants
            Object.keys(patterns).forEach(category => {
                const pattern = patterns[category];
                
                if (pattern.words) {
                    pattern.words.forEach(word => {
                        const regex = new RegExp('\\b' + word.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&') + '\\b', 'i');
                        if (regex.test(normalizedText)) {
                            score += pattern.severity;
                            detectedWords.push(word);
                            categories.push(category);
                        }
                    });
                }
                
                if (pattern.variants) {
                    pattern.variants.forEach(variant => {
                        if (normalizedText.includes(variant)) {
                            score += pattern.severity * 0.8;
                            detectedWords.push(variant);
                            categories.push(category);
                        }
                    });
                }
                
                if (pattern.patterns) {
                    pattern.patterns.forEach(regex => {
                        if (regex.test(text)) {
                            score += pattern.severity * 0.6;
                            categories.push(category);
                        }
                    });
                }
            });
            
            return {
                score: Math.min(100, score),
                detectedWords: [...new Set(detectedWords)],
                categories: [...new Set(categories)],
                isInappropriate: score > 60
            };
        }

        function updateModerationFeedback(analysis) {
            progressBarFill.style.width = '100%';
            
            setTimeout(() => {
                progressBar.style.display = 'none';
                statusText.style.display = 'none';
                
                if (analysis.isInappropriate) {
                    const warningLevel = getWarningLevel(analysis.score);
                    badwordList.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Score de risque: ${analysis.score}/100</span>
                            <span class="badge bg-${warningLevel.color}">${warningLevel.label}</span>
                        </div>
                        ${analysis.detectedWords.length > 0 ? 
                            `<div class="mt-1"><small>Éléments détectés: ${analysis.detectedWords.join(', ')}</small></div>` : 
                            ''
                        }
                        <div class="mt-2">
                            <small class="text-info">💡 Suggestions: Reformulez votre message avec un langage plus approprié.</small>
                        </div>
                    `;
                    
                    alertBox.className = `alert alert-${warningLevel.alertType} mt-2`;
                    alertBox.style.display = 'block';
                    
                    submitBtn.disabled = analysis.score > 70;
                    submitBtn.className = analysis.score > 70 ? 'btn btn-secondary' : 'btn btn-warning';
                    submitBtn.textContent = analysis.score > 70 ? 'Contenu bloqué' : 'Publier (avec avertissement)';
                } else {
                    alertBox.style.display = 'none';
                    badwordList.textContent = '';
                    submitBtn.disabled = false;
                    submitBtn.className = 'btn btn-primary';
                    submitBtn.textContent = 'Publier';
                }
            }, 200);
        }

        function getWarningLevel(score) {
            if (score < 30) return { color: 'success', label: 'Sûr', alertType: 'info' };
            if (score < 50) return { color: 'warning', label: 'Attention', alertType: 'warning' };
            if (score < 70) return { color: 'danger', label: 'Risqué', alertType: 'warning' };
            return { color: 'dark', label: 'Bloqué', alertType: 'danger' };
        }

        function debounce(func, wait) {
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(analysisTimeout);
                    func(...args);
                };
                clearTimeout(analysisTimeout);
                analysisTimeout = setTimeout(later, wait);
            };
        }

        const debouncedAnalyze = debounce((text) => analyzeContent(text), 800);

        textarea.addEventListener('input', function() {
            const text = this.value || '';
            if (text.length > 0) {
                debouncedAnalyze(text);
            } else {
                alertBox.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.className = 'btn btn-primary';
                submitBtn.textContent = 'Publier';
                progressBar.style.display = 'none';
                statusText.style.display = 'none';
            }
        });

        // Initial check
        if (textarea.value) {
            analyzeContent(textarea.value);
        }
    })();
</script>
@endpush
@endsection