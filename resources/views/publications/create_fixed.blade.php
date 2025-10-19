@extends('layouts.app')

@section('title', 'Créer une publication')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('publications.index') }}">Publications</a></li>
            <li class="breadcrumb-item active" aria-current="page">Créer une publication</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Créer une nouvelle publication</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('publications.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('contenu') is-invalid @enderror" id="contenu" name="contenu" rows="6" required>{{ old('contenu') }}</textarea>
                            
                            {{-- Moderation feedback --}}
                            <div class="mt-2">
                                <div class="progress" style="height: 4px; display: none;" id="moderation-progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="moderation-status" style="display: none;"></small>
                            </div>
                            
                            <div id="moderation-alert" class="alert mt-2" style="display: none;" role="alert">
                                <div id="moderation-details"></div>
                            </div>
                            
                            @error('contenu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Display moderation result if redirected back --}}
                        @if(session('moderation_result'))
                            <div class="alert alert-warning mb-3">
                                <h6><i class="fas fa-exclamation-triangle"></i> Analyse de modération</h6>
                                @php $result = session('moderation_result'); @endphp
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Score global:</strong> {{ $result['global_score'] }}/100<br>
                                        <strong>Action:</strong> {{ $result['action']['level'] ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        @if(!empty($result['suggestions']))
                                            <strong>Suggestions:</strong>
                                            <ul class="small mb-0">
                                                @foreach($result['suggestions'] as $category => $suggestion)
                                                    <li>{{ $suggestion['message'] ?? $category }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image (optionnelle)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            {{-- Hidden AI suggestion fields (auto-filled) --}}
                            <input type="hidden" name="event_type" id="event_type">
                            <input type="hidden" name="ai_description" id="ai_description">
                            <input type="hidden" name="ai_hashtags" id="ai_hashtags">
                            <div id="ai-suggestions" class="mt-2 d-none">
                                <div class="small text-muted">Suggestion description: <span id="ai-desc-text"></span></div>
                                <div class="small text-muted">Hashtags: <span id="ai-hashtags-text"></span></div>
                            </div>
                            <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 2MB</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Outils IA --}}
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-robot"></i> Assistant IA</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-primary btn-sm mb-2 w-100" id="analyze-ai-btn">
                                            <i class="fas fa-search"></i> Analyser avec IA
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm mb-2 w-100" id="improve-content-btn">
                                            <i class="fas fa-magic"></i> Améliorer le contenu
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-info btn-sm mb-2 w-100" id="generate-hashtags-btn">
                                            <i class="fas fa-hashtag"></i> Générer hashtags
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm mb-2 w-100" id="get-suggestions-btn">
                                            <i class="fas fa-lightbulb"></i> Suggestions de contenu
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Zone de résultats IA --}}
                                <div id="ai-results" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <div id="ai-results-content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Suggestions personnalisées --}}
                        @if(!empty($suggestions))
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-star"></i> Suggestions personnalisées</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Sujets tendance --}}
                                    @if(!empty($suggestions['trending_topics']['hot_topics']))
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary">🔥 Sujets tendance</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($suggestions['trending_topics']['hot_topics'], 0, 5) as $topic)
                                                <span class="badge bg-primary suggestion-topic" data-topic="{{ $topic }}" style="cursor: pointer;">{{ $topic }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    {{-- Hashtags tendance --}}
                                    @if(!empty($suggestions['trending_topics']['trending_hashtags']))
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-success">📈 Hashtags populaires</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($suggestions['trending_topics']['trending_hashtags'], 0, 5) as $hashtag)
                                                <span class="badge bg-success suggestion-hashtag" data-hashtag="{{ $hashtag }}" style="cursor: pointer;">{{ $hashtag }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                {{-- Templates de contenu --}}
                                @if(!empty($suggestions['template_suggestions']))
                                <div class="mt-3">
                                    <h6 class="text-info">📝 Modèles de contenu</h6>
                                    <div class="accordion" id="templatesAccordion">
                                        @foreach($suggestions['template_suggestions'] as $category => $templates)
                                            @if(is_array($templates) && count($templates) > 0)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#template-{{ $loop->index }}">
                                                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                                                    </button>
                                                </h2>
                                                <div id="template-{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#templatesAccordion">
                                                    <div class="accordion-body">
                                                        @foreach(array_slice($templates, 0, 3) as $template)
                                                            <div class="template-suggestion mb-2 p-2 bg-light rounded" style="cursor: pointer;" data-template="{{ $template }}">
                                                                <small>{{ $template }}</small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('publications.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Publier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- JavaScript pour l'analyse d'image existante --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    const fileInput = document.getElementById('image');
    const aiSection = document.getElementById('ai-suggestions');
    const aiDesc = document.getElementById('ai-desc-text');
    const aiTags = document.getElementById('ai-hashtags-text');
    const eventTypeInput = document.getElementById('event_type');
    const aiDescInput = document.getElementById('ai_description');
    const aiHashtagsInput = document.getElementById('ai_hashtags');

    if (!fileInput) return;

    fileInput.addEventListener('change', function(){
        const file = this.files && this.files[0];
        if (!file) return;

        // Send filename and content to server analyze endpoint
        const formData = new FormData();
        formData.append('filename', file.name);
        formData.append('contenu', document.getElementById('contenu').value || '');

        fetch('{{ route('publications.analyze-image') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        }).then(res => res.json()).then(json => {
            if (json) {
                eventTypeInput.value = json.event_type || '';
                aiDescInput.value = json.description || '';
                aiHashtagsInput.value = JSON.stringify(json.hashtags || []);

                aiDesc.textContent = json.description || '';
                aiTags.textContent = (json.hashtags || []).join(' ');
                aiSection.classList.remove('d-none');

                // Fill title and content immediately if empty
                const titreInput = document.getElementById('titre');
                const contenuInput = document.getElementById('contenu');
                const descVal = json.description || '';
                
                if (titreInput && !titreInput.value.trim()) {
                    titreInput.value = descVal.split(' ').slice(0,6).join(' ').replace(/\.$/, '');
                }
                if (contenuInput && !contenuInput.value.trim()) {
                    contenuInput.value = descVal;
                }
            }
        }).catch(err => {
            console.error('AI analyze failed', err);
        });
    });
});
</script>

{{-- JavaScript pour la modération intelligente --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intelligent moderation for publications
    const titreInput = document.getElementById('titre');
    const contenuInput = document.getElementById('contenu');
    const moderationProgress = document.getElementById('moderation-progress');
    const moderationStatus = document.getElementById('moderation-status');
    const moderationAlert = document.getElementById('moderation-alert');
    const moderationDetails = document.getElementById('moderation-details');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    let moderationTimeout;
    let lastAnalyzedContent = '';

    function analyzeModerationContent() {
        const titre = titreInput.value || '';
        const contenu = contenuInput.value || '';
        const combinedText = (titre + ' ' + contenu).trim();
        
        if (combinedText === lastAnalyzedContent || combinedText.length < 3) {
            return;
        }
        
        lastAnalyzedContent = combinedText;
        
        // Show progress
        moderationProgress.style.display = 'block';
        moderationStatus.style.display = 'block';
        moderationStatus.textContent = 'Analyse du contenu...';
        moderationProgress.querySelector('.progress-bar').style.width = '30%';
        
        // Call server endpoint for analysis
        fetch('{{ route('publications.analyze-content') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                titre: titre,
                contenu: contenu
            })
        })
        .then(response => response.json())
        .then(data => {
            updateModerationUI(data);
        })
        .catch(error => {
            console.error('Moderation analysis failed:', error);
            hideModerationUI();
        });
    }

    function updateModerationUI(analysis) {
        moderationProgress.querySelector('.progress-bar').style.width = '100%';
        
        setTimeout(() => {
            moderationProgress.style.display = 'none';
            moderationStatus.style.display = 'none';
            
            if (analysis.score > 30) {
                const warningConfig = getWarningConfig(analysis.warning_level);
                
                moderationDetails.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span><strong>Score de modération:</strong> ${analysis.score}/100</span>
                        <span class="badge bg-${warningConfig.badgeColor}">${warningConfig.label}</span>
                    </div>
                    ${analysis.message ? `<div class="mb-2">${analysis.message}</div>` : ''}
                    ${analysis.detected_categories && analysis.detected_categories.length > 0 ? 
                        `<div class="small text-muted">Catégories détectées: ${analysis.detected_categories.join(', ')}</div>` : 
                        ''
                    }
                    <div class="mt-2">
                        <small class="text-info">💡 <strong>Conseil:</strong> ${getRecommendation(analysis.warning_level)}</small>
                    </div>
                `;
                
                moderationAlert.className = `alert alert-${warningConfig.alertType} mt-2`;
                moderationAlert.style.display = 'block';
                
                // Update submit button
                if (analysis.warning_level === 'danger') {
                    submitBtn.disabled = true;
                    submitBtn.className = 'btn btn-secondary';
                    submitBtn.textContent = 'Publication bloquée';
                } else if (analysis.warning_level === 'warning') {
                    submitBtn.disabled = false;
                    submitBtn.className = 'btn btn-warning';
                    submitBtn.textContent = 'Publier (avec révision)';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.className = 'btn btn-primary';
                    submitBtn.textContent = 'Publier';
                }
            } else {
                hideModerationUI();
            }
        }, 300);
    }

    function hideModerationUI() {
        moderationAlert.style.display = 'none';
        moderationProgress.style.display = 'none';
        moderationStatus.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.className = 'btn btn-primary';
        submitBtn.textContent = 'Publier';
    }

    function getWarningConfig(level) {
        const configs = {
            'safe': { badgeColor: 'success', label: 'Sûr', alertType: 'info' },
            'caution': { badgeColor: 'warning', label: 'Attention', alertType: 'warning' },
            'warning': { badgeColor: 'danger', label: 'Problématique', alertType: 'warning' },
            'danger': { badgeColor: 'dark', label: 'Bloqué', alertType: 'danger' }
        };
        return configs[level] || configs['safe'];
    }

    function getRecommendation(level) {
        const recommendations = {
            'safe': 'Votre contenu respecte nos directives.',
            'caution': 'Vérifiez le ton et le langage utilisé.',
            'warning': 'Reformulez les passages problématiques avant publication.',
            'danger': 'Réécrivez complètement votre contenu pour respecter nos conditions.'
        };
        return recommendations[level] || recommendations['safe'];
    }

    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(moderationTimeout);
                func(...args);
            };
            clearTimeout(moderationTimeout);
            moderationTimeout = setTimeout(later, wait);
        };
    }

    const debouncedAnalyze = debounce(analyzeModerationContent, 1000);

    // Add event listeners
    if (titreInput) {
        titreInput.addEventListener('input', debouncedAnalyze);
    }
    if (contenuInput) {
        contenuInput.addEventListener('input', debouncedAnalyze);
    }

    // Initial analysis if there's content
    if ((titreInput && titreInput.value) || (contenuInput && contenuInput.value)) {
        setTimeout(analyzeModerationContent, 500);
    }
});
</script>

{{-- Chargement du JavaScript IA externe --}}
<script src="{{ asset('js/publication-ai.js') }}"></script>
@endpush
