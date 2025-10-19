/**
 * JavaScript pour les fonctionnalités IA des publications
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🤖 Système IA Publications chargé');

    // Éléments DOM
    const analyzeAIBtn = document.getElementById('analyze-ai-btn');
    const improveContentBtn = document.getElementById('improve-content-btn');
    const generateHashtagsBtn = document.getElementById('generate-hashtags-btn');
    const getSuggestionsBtn = document.getElementById('get-suggestions-btn');
    const aiResults = document.getElementById('ai-results');
    const aiResultsContent = document.getElementById('ai-results-content');

    // Vérifier que les éléments existent
    if (!aiResults || !aiResultsContent) {
        console.error('❌ Éléments IA manquants dans le DOM');
        return;
    }

    // Fonction utilitaire pour obtenir le token CSRF
    function getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            console.error('❌ Token CSRF manquant');
            return null;
        }
        return token.getAttribute('content');
    }

    // Fonction pour afficher le chargement
    function showAILoading(message) {
        console.log('⏳ ' + message);
        aiResultsContent.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                <span>${message}</span>
            </div>
        `;
        aiResults.style.display = 'block';
        aiResults.querySelector('.alert').className = 'alert alert-info';
    }

    // Fonction pour afficher les résultats
    function showAIResult(message, type) {
        type = type || 'info';
        console.log('📝 Résultat IA:', type, message);
        aiResultsContent.innerHTML = message;
        aiResults.style.display = 'block';
        aiResults.querySelector('.alert').className = `alert alert-${type}`;
    }

    // Fonction pour faire les requêtes AJAX
    function makeAIRequest(url, data, successCallback) {
        const csrfToken = getCSRFToken();
        if (!csrfToken) {
            showAIResult('Erreur: Token CSRF manquant', 'danger');
            return;
        }

        console.log('🚀 Requête IA vers:', url, data);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('📡 Réponse reçue:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Données reçues:', data);
            if (data.success) {
                successCallback(data);
            } else {
                showAIResult('Erreur: ' + (data.error || 'Erreur inconnue'), 'danger');
            }
        })
        .catch(error => {
            console.error('❌ Erreur requête:', error);
            showAIResult('Erreur de connexion: ' + error.message, 'danger');
        });
    }

    // 1. Analyse complète avec IA
    if (analyzeAIBtn) {
        analyzeAIBtn.addEventListener('click', function() {
            console.log('🔍 Analyse IA demandée');
            
            const titre = document.getElementById('titre').value || '';
            const contenu = document.getElementById('contenu').value || '';
            
            if (!titre.trim() && !contenu.trim()) {
                showAIResult('⚠️ Veuillez saisir un titre et/ou du contenu à analyser.', 'warning');
                return;
            }
            
            showAILoading('Analyse IA en cours...');
            
            makeAIRequest('/publications/analyze-ai', { titre, contenu }, function(data) {
                displayAIAnalysis(data.analysis);
            });
        });
    }

    // 2. Amélioration du contenu
    if (improveContentBtn) {
        improveContentBtn.addEventListener('click', function() {
            console.log('✨ Amélioration de contenu demandée');
            
            const titre = document.getElementById('titre').value || '';
            const contenu = document.getElementById('contenu').value || '';
            
            if (!titre.trim() && !contenu.trim()) {
                showAIResult('⚠️ Veuillez saisir du contenu à améliorer.', 'warning');
                return;
            }
            
            // Menu de sélection simplifié
            const types = {
                '1': 'grammar',
                '2': 'style', 
                '3': 'engagement',
                '4': 'seo'
            };
            
            const choice = prompt('Choisissez le type d\'amélioration:\n1. Grammaire\n2. Style\n3. Engagement\n4. SEO\n\nTapez le numéro (1-4):');
            
            if (!choice || !types[choice]) {
                showAIResult('⚠️ Choix invalide. Veuillez choisir 1, 2, 3 ou 4.', 'warning');
                return;
            }
            
            const improvementType = types[choice];
            showAILoading('Amélioration du contenu en cours...');
            
            makeAIRequest('/publications/improve-content', { 
                titre, 
                contenu, 
                improvement_type: improvementType 
            }, function(data) {
                displayContentImprovement(data.improved_content, improvementType);
            });
        });
    }

    // 3. Génération de hashtags
    if (generateHashtagsBtn) {
        generateHashtagsBtn.addEventListener('click', function() {
            console.log('#️⃣ Génération de hashtags demandée');
            
            const titre = document.getElementById('titre').value || '';
            const contenu = document.getElementById('contenu').value || '';
            
            if (!titre.trim() && !contenu.trim()) {
                showAIResult('⚠️ Veuillez saisir du contenu pour générer des hashtags.', 'warning');
                return;
            }
            
            showAILoading('Génération de hashtags...');
            
            makeAIRequest('/publications/generate-hashtags', { titre, contenu }, function(data) {
                displayHashtags(data.hashtags, data.category);
            });
        });
    }

    // 4. Suggestions de contenu
    if (getSuggestionsBtn) {
        getSuggestionsBtn.addEventListener('click', function() {
            console.log('💡 Suggestions de contenu demandées');
            
            showAILoading('Génération de suggestions...');
            
            makeAIRequest('/publications/generate-suggestions', {}, function(data) {
                displayContentSuggestions(data.suggestions);
            });
        });
    }

    // Fonctions d'affichage des résultats
    function displayAIAnalysis(analysis) {
        let html = '<h6><i class="fas fa-chart-line"></i> Analyse IA complète</h6>';
        
        // Score de qualité
        if (analysis.quality_score) {
            const score = analysis.quality_score;
            const badgeColor = getScoreBadgeColor(score.overall_score);
            html += `
                <div class="mb-3">
                    <strong>Score de qualité:</strong> ${score.overall_score}/10 
                    <span class="badge bg-${badgeColor}">${score.grade}</span>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-${badgeColor}" style="width: ${score.overall_score * 10}%"></div>
                    </div>
                </div>
            `;
        }
        
        // Analyse du contenu
        if (analysis.content_analysis) {
            const content = analysis.content_analysis;
            html += `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Sentiment:</strong> ${content.sentiment}<br>
                        <strong>Ton:</strong> ${content.tone}<br>
                        <strong>Lisibilité:</strong> ${content.readability_score}/10
                    </div>
                    <div class="col-md-6">
                        <strong>Potentiel d'engagement:</strong> ${content.engagement_potential}/10<br>
                        <strong>Qualité linguistique:</strong> ${content.language_quality}/10
                    </div>
                </div>
            `;
        }
        
        // Catégorie détectée
        if (analysis.category) {
            html += `<div class="mb-2"><strong>Catégorie:</strong> ${analysis.category.primary_category} (${Math.round(analysis.category.confidence_score * 100)}% de confiance)</div>`;
        }
        
        showAIResult(html, 'success');
    }

    function displayContentImprovement(improvement, type) {
        let html = `<h6><i class="fas fa-magic"></i> ${improvement.type}</h6>`;
        
        if (improvement.content) {
            html += `
                <div class="mb-3">
                    <strong>Contenu amélioré:</strong>
                    <div class="bg-light p-2 rounded mt-1">
                        <small>${improvement.content}</small>
                    </div>
                    <button class="btn btn-sm btn-success mt-2" onclick="applyImprovement('${escapeHtml(improvement.content)}', '${escapeHtml(improvement.title || '')}')">
                        <i class="fas fa-check"></i> Appliquer cette amélioration
                    </button>
                </div>
            `;
        }
        
        if (improvement.tips && improvement.tips.length > 0) {
            html += `
                <div class="mb-3">
                    <strong>Conseils:</strong>
                    <ul class="small mb-0">
                        ${improvement.tips.map(tip => `<li>${tip}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        showAIResult(html, 'success');
    }

    function displayHashtags(hashtags, category) {
        let html = '<h6><i class="fas fa-hashtag"></i> Hashtags générés</h6>';
        
        if (category && category.primary_category) {
            html += `<div class="mb-2"><strong>Catégorie détectée:</strong> ${category.primary_category}</div>`;
        }
        
        if (hashtags) {
            Object.keys(hashtags).forEach(type => {
                if (hashtags[type] && hashtags[type].length > 0) {
                    html += `
                        <div class="mb-2">
                            <strong>${type.replace('_', ' ').toUpperCase()}:</strong><br>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                ${hashtags[type].map(tag => 
                                    `<span class="badge bg-primary hashtag-suggestion" style="cursor: pointer;" onclick="addHashtag('${tag}')">${tag}</span>`
                                ).join('')}
                            </div>
                        </div>
                    `;
                }
            });
            
            html += `
                <button class="btn btn-sm btn-success mt-2" onclick="applyAllHashtags('${escapeHtml(JSON.stringify(hashtags))}')">
                    <i class="fas fa-check"></i> Ajouter tous les hashtags
                </button>
            `;
        }
        
        showAIResult(html, 'success');
    }

    function displayContentSuggestions(suggestions) {
        let html = '<h6><i class="fas fa-lightbulb"></i> Nouvelles suggestions</h6>';
        
        if (suggestions && suggestions.inspiration_prompts && suggestions.inspiration_prompts.creative_prompts) {
            html += `
                <div class="mb-3">
                    <strong>Prompts d'inspiration:</strong>
                    <ul class="small">
                        ${suggestions.inspiration_prompts.creative_prompts.slice(0, 3).map(prompt => 
                            `<li class="suggestion-prompt" style="cursor: pointer;" onclick="applyPrompt('${escapeHtml(prompt)}')">${prompt}</li>`
                        ).join('')}
                    </ul>
                </div>
            `;
        }
        
        showAIResult(html, 'info');
    }

    // Fonctions utilitaires
    function getScoreBadgeColor(score) {
        if (score >= 8) return 'success';
        if (score >= 6) return 'warning';
        return 'danger';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Fonctions globales pour les boutons dynamiques
    window.applyImprovement = function(content, title) {
        console.log('✅ Application de l\'amélioration');
        if (content) {
            const contenuField = document.getElementById('contenu');
            if (contenuField) contenuField.value = content;
        }
        if (title) {
            const titreField = document.getElementById('titre');
            if (titreField) titreField.value = title;
        }
        showAIResult('✅ Améliorations appliquées !', 'success');
        setTimeout(() => aiResults.style.display = 'none', 2000);
    };

    window.applyAllHashtags = function(hashtagsJson) {
        console.log('✅ Application des hashtags');
        try {
            const hashtags = JSON.parse(hashtagsJson);
            const allTags = Object.values(hashtags).flat();
            const contenuField = document.getElementById('contenu');
            if (contenuField) {
                contenuField.value += '\n\n' + allTags.join(' ');
            }
            showAIResult('✅ Hashtags ajoutés !', 'success');
            setTimeout(() => aiResults.style.display = 'none', 2000);
        } catch (e) {
            console.error('❌ Erreur parsing hashtags:', e);
            showAIResult('❌ Erreur lors de l\'ajout des hashtags', 'danger');
        }
    };

    window.addHashtag = function(hashtag) {
        console.log('✅ Ajout hashtag:', hashtag);
        const contenuField = document.getElementById('contenu');
        if (contenuField) {
            contenuField.value += ' ' + hashtag;
        }
    };

    window.applyPrompt = function(prompt) {
        console.log('✅ Application du prompt:', prompt);
        const contenuField = document.getElementById('contenu');
        if (contenuField) {
            contenuField.value = prompt;
            contenuField.focus();
        }
    };

    // Gestion des clics sur les suggestions existantes
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('suggestion-topic')) {
            const topic = e.target.dataset.topic;
            const contenuField = document.getElementById('contenu');
            if (contenuField && topic) {
                contenuField.value += (contenuField.value ? '\n\n' : '') + `Parlons de ${topic}... `;
                contenuField.focus();
            }
        }
        
        if (e.target.classList.contains('suggestion-hashtag')) {
            const hashtag = e.target.dataset.hashtag;
            const contenuField = document.getElementById('contenu');
            if (contenuField && hashtag) {
                contenuField.value += ' ' + hashtag;
            }
        }
        
        if (e.target.classList.contains('template-suggestion')) {
            const template = e.target.dataset.template;
            const contenuField = document.getElementById('contenu');
            if (contenuField && template) {
                contenuField.value = template;
                contenuField.focus();
            }
        }
    });

    console.log('✅ Système IA Publications initialisé avec succès');
});
