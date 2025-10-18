@extends('layouts.app')

@section('title', 'Nouvelle Réclamation - ECO EVENT')

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
    .form-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    .submit-btn {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(220,53,69,0.4);
        color: white;
    }
    .back-btn {
        background: #6c757d;
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(108,117,125,0.4);
        color: white;
    }
    @media (max-width: 768px) {
        .hero-section { min-height: 30vh; }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section text-white">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3" data-aos="fade-up">Nouvelle Réclamation</h1>
                <p class="lead mb-0" data-aos="fade-up" data-aos-delay="200">Partagez votre préoccupation pour que nous puissions l'examiner et y répondre.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-exclamation-triangle fa-5x opacity-75" data-aos="zoom-in" data-aos-delay="400"></i>
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

<!-- Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="form-card p-4" data-aos="fade-up">
                    <form action="{{ route('reclamations.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="sujet" class="form-label fw-bold">Sujet de la réclamation</label>
                            <input type="text" name="sujet" id="sujet" class="form-control @error('sujet') is-invalid @enderror" placeholder="Décrivez brièvement le sujet..." value="{{ old('sujet') }}" required>
                            @error('sujet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description détaillée</label>
                            <textarea name="description" id="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Expliquez en détail votre réclamation..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-center">
                            <button type="submit" class="submit-btn text-white px-5 py-3">
                                <i class="fas fa-paper-plane me-2"></i>Soumettre la réclamation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });
</script>
@endpush
