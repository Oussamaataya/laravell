@extends('layouts.back')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Scanner les billets</h3>
                    <h6 class="font-weight-normal mb-0">
                        @if($event)
                            Événement: {{ $event->title }}
                        @else
                            Sélectionnez un événement pour commencer le scan
                        @endif
                    </h6>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Sélection de l'événement -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Sélectionner un événement</h4>
                    <form action="{{ route('admin.tickets.scan') }}" method="GET">
                        <div class="form-group">
                            <select name="event_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Choisir un événement --</option>
                                @foreach($events as $e)
                                    <option value="{{ $e->id }}" {{ $event && $event->id == $e->id ? 'selected' : '' }}>
                                        {{ $e->title }} - {{ $e->start_date->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($event)
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Total Inscrits</h4>
                        <h2 class="mb-5">{{ $stats['total'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Présents</h4>
                        <h2 class="mb-5">{{ $stats['checked_in'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">En Attente</h4>
                        <h2 class="mb-5">{{ $stats['pending'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Annulés</h4>
                        <h2 class="mb-5">{{ $stats['cancelled'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner QR Code -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Scanner via Caméra</h4>
                        <div id="qr-reader" style="width: 100%; height: 400px;"></div>
                        <div class="mt-3">
                            <button id="start-scan" class="btn btn-primary">
                                <i class="mdi mdi-camera"></i> Démarrer le scan
                            </button>
                            <button id="stop-scan" class="btn btn-danger" style="display: none;">
                                <i class="mdi mdi-stop"></i> Arrêter le scan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Saisie manuelle</h4>
                        <form id="manual-scan-form">
                            <div class="form-group">
                                <label>Code du billet</label>
                                <input type="text" id="ticket-code-input" class="form-control" 
                                       placeholder="EVT-2025-XXX-XXXXXX" autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check"></i> Valider le billet
                            </button>
                        </form>

                        <!-- Résultat de la validation -->
                        <div id="validation-result" class="mt-4" style="display: none;">
                            <div id="result-content" class="alert"></div>
                        </div>
                    </div>
                </div>

                <!-- Dernières validations -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title">Dernières validations</h4>
                        <ul id="recent-validations" class="list-unstyled">
                            <!-- Les validations seront ajoutées ici dynamiquement -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<!-- Html5QrcodeScanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;

// Fonction pour valider un billet
async function validateTicket(ticketCode) {
    try {
        const response = await fetch('{{ route("admin.tickets.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ticket_code: ticketCode })
        });

        const result = await response.json();
        displayValidationResult(result);
        
        if (result.success) {
            addToRecentValidations(result.data);
            playSuccessSound();
        } else {
            playErrorSound();
        }

        return result;
    } catch (error) {
        console.error('Erreur de validation:', error);
        displayValidationResult({
            success: false,
            message: 'Erreur de connexion au serveur'
        });
        playErrorSound();
    }
}

// Afficher le résultat de la validation
function displayValidationResult(result) {
    const resultDiv = document.getElementById('validation-result');
    const contentDiv = document.getElementById('result-content');
    
    resultDiv.style.display = 'block';
    
    if (result.success) {
        contentDiv.className = 'alert alert-success';
        contentDiv.innerHTML = `
            <h5><i class="mdi mdi-check-circle"></i> ${result.message}</h5>
            <p class="mb-1"><strong>Participant:</strong> ${result.data.user_name}</p>
            <p class="mb-1"><strong>Événement:</strong> ${result.data.event_title}</p>
            <p class="mb-0"><strong>Code:</strong> ${result.data.ticket_code}</p>
        `;
    } else {
        contentDiv.className = 'alert alert-danger';
        contentDiv.innerHTML = `
            <h5><i class="mdi mdi-close-circle"></i> ${result.message}</h5>
            ${result.data ? `
                <p class="mb-1"><strong>Participant:</strong> ${result.data.user_name}</p>
                <p class="mb-0"><strong>Événement:</strong> ${result.data.event_title}</p>
            ` : ''}
        `;
    }

    // Masquer après 5 secondes
    setTimeout(() => {
        resultDiv.style.display = 'none';
    }, 5000);
}

// Ajouter aux validations récentes
function addToRecentValidations(data) {
    const list = document.getElementById('recent-validations');
    const li = document.createElement('li');
    li.className = 'border-bottom pb-2 mb-2';
    li.innerHTML = `
        <div class="d-flex justify-content-between">
            <div>
                <strong>${data.user_name}</strong><br>
                <small class="text-muted">${data.ticket_code}</small>
            </div>
            <div class="text-right">
                <small class="text-success">✓ Validé</small><br>
                <small class="text-muted">${data.checked_in_at}</small>
            </div>
        </div>
    `;
    list.insertBefore(li, list.firstChild);

    // Garder seulement les 5 dernières
    while (list.children.length > 5) {
        list.removeChild(list.lastChild);
    }
}

// Sons de feedback
function playSuccessSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRhIAAABXQVZFZm10IBIAAAABAAEAQB8AAEAfAAABAAgAAABmYWN0BAAAAAAAAABkYXRh');
    audio.play().catch(() => {});
}

function playErrorSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRhIAAABXQVZFZm10IBIAAAABAAEAQB8AAEAfAAABAAgAAABmYWN0BAAAAAAAAABkYXRh');
    audio.play().catch(() => {});
}

// Gestion du scan par caméra
document.getElementById('start-scan')?.addEventListener('click', function() {
    html5QrCode = new Html5Qrcode("qr-reader");
    
    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        (decodedText) => {
            try {
                const data = JSON.parse(decodedText);
                if (data.ticket_code) {
                    validateTicket(data.ticket_code);
                }
            } catch (e) {
                // Si ce n'est pas du JSON, utiliser directement le texte
                validateTicket(decodedText);
            }
        }
    ).then(() => {
        document.getElementById('start-scan').style.display = 'none';
        document.getElementById('stop-scan').style.display = 'inline-block';
    }).catch(err => {
        alert('Erreur d\'accès à la caméra: ' + err);
    });
});

document.getElementById('stop-scan')?.addEventListener('click', function() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            document.getElementById('start-scan').style.display = 'inline-block';
            document.getElementById('stop-scan').style.display = 'none';
        });
    }
});

// Gestion du formulaire de saisie manuelle
document.getElementById('manual-scan-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const ticketCode = document.getElementById('ticket-code-input').value.trim();
    if (ticketCode) {
        validateTicket(ticketCode);
        document.getElementById('ticket-code-input').value = '';
    }
});

// Support du scan avec lecteur de code-barres
document.getElementById('ticket-code-input')?.addEventListener('input', function(e) {
    const value = e.target.value;
    // Si la valeur se termine par Enter (lecteur de code-barres)
    if (value.includes('\n') || value.includes('\r')) {
        const ticketCode = value.replace(/[\n\r]/g, '').trim();
        if (ticketCode) {
            validateTicket(ticketCode);
            e.target.value = '';
        }
    }
});
</script>
@endpush
@endsection
