@extends('layouts.app')

@section('title', 'Avis - ' . $reclamation->sujet . ' - ECO EVENT')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --border-radius: 15px;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    .hero-section {
        background: var(--primary-gradient);
        min-height: 40vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .reclamation-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    .avis-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
    }
    .avis-card:hover {
        transform: translateY(-5px);
    }
    .star-rating {
        color: #ffc107;
    }
    .star-rating-input {
        position: relative;
    }
    .star-input {
        display: none;
    }
    .star-label {
        cursor: pointer;
        font-size: 2rem;
        color: #ddd;
        transition: color 0.2s;
        margin: 0 2px;
    }
    .star-label:hover,
    .star-label:hover ~ .star-label,
    .star-input:checked ~ .star-label {
        color: #ffc107;
    }
    .back-btn {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(220,53,69,0.4);
        color: white;
    }
    @media (max-width: 768px) {
        .hero-section { min-height: 30vh; }
        .star-label { font-size: 1.5rem; }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section text-white">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3" data-aos="fade-up">{{ $reclamation->sujet }}</h1>
                <p class="lead mb-0" data-aos="fade-up" data-aos-delay="200">{{ $reclamation->description }}</p>
            </div>
            <div class="col-lg-4 text-center">
                <div class="status-badge position-relative">
                    @if($reclamation->statut == 'traitee')
                        <span class="badge bg-success px-4 py-3 rounded-pill fw-bold fs-5">
                            <i class="fas fa-check me-2"></i>Traité
                        </span>
                    @elseif($reclamation->statut == 'en_cours')
                        <span class="badge bg-warning px-4 py-3 rounded-pill fw-bold fs-5">
                            <i class="fas fa-clock me-2"></i>En Cours
                        </span>
                    @else
                        <span class="badge bg-secondary px-4 py-3 rounded-pill fw-bold fs-5">
                            <i class="fas fa-hourglass-half me-2"></i>En Attente
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Back Navigation -->
<section class="py-3 bg-light">
    <div class="container">
        <a href="{{ route('reclamations.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Retour aux Réclamations
        </a>
    </div>
</section>

<!-- Reclamation Details -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="reclamation-card p-4 mb-5" data-aos="fade-up">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="fw-bold text-dark mb-3">{{ $reclamation->sujet }}</h3>
                            <p class="text-muted mb-4">{{ $reclamation->description }}</p>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.1rem; font-weight: bold;">
                                    {{ substr($reclamation->user->name ?? 'N/A', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $reclamation->user->name ?? 'Utilisateur Anonyme' }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $reclamation->created_at->format('d M Y à H:i') }}
                                        <i class="fas fa-clock ms-3 me-1"></i>{{ $reclamation->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mt-3">
                                <small class="text-muted">Statut:</small><br>
                                @if($reclamation->statut == 'traitee')
                                    <span class="badge bg-success px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-check me-1"></i>{{ ucfirst($reclamation->statut) }}
                                    </span>
                                @elseif($reclamation->statut == 'en_cours')
                                    <span class="badge bg-warning px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-clock me-1"></i>{{ ucfirst($reclamation->statut) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-hourglass-half me-1"></i>{{ ucfirst($reclamation->statut) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Avis Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="fw-bold text-center mb-5" data-aos="fade-up">
                    <i class="fas fa-comments text-primary me-2"></i>Avis et Commentaires
                </h2>
                
                @if($reclamation->avis->count() > 0)
                    <div id="avis-container" class="row g-4">
                        @foreach($reclamation->avis as $avis)
                            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="avis-card p-4 h-100">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                                            {{ substr($avis->user->name ?? 'N/A', 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $avis->user->name ?? 'Utilisateur Anonyme' }}</h6>
                                            <div class="star-rating mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $avis->note ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor>
                                            </div>
                                            <small class="text-muted">{{ $avis->created_at->format('d M Y à H:i') }}</small>
                                        </div>
                                    </div>
                                    <p class="text-dark mb-0">{{ $avis->commentaire }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div id="no-avis" class="text-center py-5" data-aos="fade-up">
                        <i class="fas fa-comment-slash fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">Aucun avis pour cette réclamation</h4>
                        <p class="text-muted">Soyez le premier à laisser un commentaire !</p>
                    </div>
                @endif

                @auth
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-8">
                        <h4 class="fw-bold text-center mb-4" data-aos="fade-up">
                            <i class="fas fa-star text-warning me-2"></i>Ajouter votre avis
                        </h4>
                        <form id="avis-form" action="{{ route('reclamations.avis.store', $reclamation) }}" method="POST" class="reclamation-card p-4" data-aos="fade-up">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold">Note (1-5 étoiles)</label>
                                <div class="star-rating-input d-flex justify-content-center mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" id="star{{ $i }}" name="note" value="{{ $i }}" class="star-input" required>
                                        <label for="star{{ $i }}" class="star-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="commentaire" class="form-label fw-bold">Votre commentaire</label>
                                <textarea name="commentaire" id="commentaire" rows="4" class="form-control @error('commentaire') is-invalid @enderror" placeholder="Partagez votre expérience..." required>{{ old('commentaire') }}</textarea>
                                @error('commentaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4 py-2" id="submit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer votre avis
                                </button>
                            </div>
                        </form>
                        <div id="form-message" class="alert d-none mt-3" role="alert"></div>
                    </div>
                </div>
                @endauth

                @guest
                <div class="text-center py-5 mt-4" data-aos="fade-up">
                    <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Connectez-vous pour ajouter un avis</h5>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary mt-2">Se connecter</a>
                </div>
                @endguest
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; 2024 ECO EVENT. Tous droits réservés.</p>
    </div>
</footer>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('avis-form');
        const submitBtn = document.getElementById('submit-btn');
        const messageDiv = document.getElementById('form-message');

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Check if note is selected
                const selectedNote = document.querySelector('input[name="note"]:checked');
                if (!selectedNote) {
                    showMessage('Veuillez sélectionner une note.', 'danger');
                    return;
                }

                const formData = new FormData(form);
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
                messageDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                messageDiv.classList.add('d-none');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Build new avis HTML
                        const avis = data.avis;
                        const userName = avis.user.name || 'Utilisateur Anonyme';
                        const userInitial = userName.charAt(0).toUpperCase();
                        const starsHtml = Array.from({length: 5}, (_, i) => 
                            `<i class="fas fa-star ${i < avis.note ? 'text-warning' : 'text-muted'}"></i>`
                        ).join('');
                        const date = new Date(avis.created_at).toLocaleString('fr-FR', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }).replace(' à ', ' à '); // Adjust format to match 'd M Y à H:i'

                        const newAvisHtml = `
                            <div class="col-lg-6" data-aos="fade-up">
                                <div class="avis-card p-4 h-100">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                                            ${userInitial}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">${userName}</h6>
                                            <div class="star-rating mb-1">
                                                ${starsHtml}
                                            </div>
                                            <small class="text-muted">${date}</small>
                                        </div>
                                    </div>
                                    <p class="text-dark mb-0">${avis.commentaire}</p>
                                </div>
                            </div>
                        `;

                        const noAvisDiv = document.getElementById('no-avis');
                        const avisContainer = document.getElementById('avis-container');

                        if (noAvisDiv) {
                            // First avis: replace no-avis with container and add new
                            noAvisDiv.outerHTML = '<div id="avis-container" class="row g-4"></div>';
                            document.getElementById('avis-container').innerHTML = newAvisHtml;
                        } else if (avisContainer) {
                            // Append to existing
                            avisContainer.insertAdjacentHTML('beforeend', newAvisHtml);
                        }

                        // Reset form
                        form.reset();
                        // Uncheck all stars (since reset may not for radios in some browsers)
                        document.querySelectorAll('input[name="note"]').forEach(radio => radio.checked = false);

                        showMessage(data.message, 'success');
                    } else {
                        // Handle errors
                        let errorMsg = 'Une erreur est survenue.';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('<br>');
                        }
                        showMessage(errorMsg, 'danger');
                    }
                } catch (error) {
                    showMessage('Erreur de connexion. Veuillez réessayer.', 'danger');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer votre avis';
                }
            });
        }

        function showMessage(msg, type) {
            messageDiv.innerHTML = msg;
            messageDiv.className = `alert alert-${type} mt-3`;
            messageDiv.classList.remove('d-none');
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.classList.add('d-none');
                }, 3000);
            }
        }
    });
</script>
@endpush
