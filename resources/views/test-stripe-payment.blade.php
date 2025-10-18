@extends('layouts.base')

@section('title', 'Test Don Stripe')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">🧪 Test de Paiement Stripe</h2>
                        
                        <div class="alert alert-info">
                            <h5>📋 Étapes du processus :</h5>
                            <ol>
                                <li>Vous remplissez le formulaire ci-dessous</li>
                                <li>Vous cliquez sur "Tester le Paiement Stripe"</li>
                                <li><strong>Vous êtes redirigé vers Stripe Checkout</strong> (page externe de Stripe)</li>
                                <li>Vous entrez une carte de test</li>
                                <li>Vous revenez sur notre site avec confirmation</li>
                            </ol>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Formulaire de test -->
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">Simuler un Don</h4>
                            </div>
                            <div class="card-body">
                                @php
                                    // Récupérer la première campagne active
                                    $campagne = \App\Models\Campagne::where('statut', 'active')->first();
                                @endphp

                                @if($campagne)
                                    <div class="mb-3">
                                        <strong>Campagne :</strong> {{ $campagne->nom }}<br>
                                        <strong>Objectif :</strong> {{ number_format($campagne->montant_objectif, 2) }} €
                                    </div>

                                    <form action="{{ route('collectes.donate', $campagne) }}" method="POST">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="montant">Montant du don (€)</label>
                                            <input type="number" 
                                                   class="form-control form-control-lg" 
                                                   id="montant" 
                                                   name="montant" 
                                                   value="10" 
                                                   min="1" 
                                                   step="0.01" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label for="message">Message (optionnel)</label>
                                            <textarea class="form-control" 
                                                      id="message" 
                                                      name="message" 
                                                      rows="3" 
                                                      placeholder="Laissez un message..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-lg btn-block">
                                            <i class="mdi mdi-credit-card"></i>
                                            Tester le Paiement Stripe
                                        </button>
                                    </form>

                                    <div class="alert alert-warning mt-4">
                                        <h6>⚠️ Ce qui va se passer :</h6>
                                        <p class="mb-0">
                                            Après avoir cliqué sur le bouton, vous serez redirigé vers 
                                            <strong>checkout.stripe.com</strong> (le site de Stripe).
                                            C'est là que vous entrerez les informations de carte bancaire.
                                        </p>
                                    </div>

                                    <div class="card border-info mt-3">
                                        <div class="card-body">
                                            <h6 class="text-info">💳 Carte de test à utiliser :</h6>
                                            <ul class="mb-0">
                                                <li><strong>Numéro :</strong> 4242 4242 4242 4242</li>
                                                <li><strong>Date :</strong> 12/25 (ou toute date future)</li>
                                                <li><strong>CVC :</strong> 123</li>
                                                <li><strong>Code postal :</strong> 12345</li>
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        <h5>❌ Aucune campagne active trouvée</h5>
                                        <p>Veuillez créer une campagne active dans l'administration pour tester les dons.</p>
                                        <a href="{{ route('admin.campagnes.create') }}" class="btn btn-primary">
                                            Créer une Campagne
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Informations de débogage -->
                        <div class="card border-secondary mt-4">
                            <div class="card-header">
                                🔧 Informations de Configuration
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Clé Stripe configurée :</strong></td>
                                        <td>
                                            @if(config('services.stripe.key'))
                                                <span class="badge badge-success">✅ Oui</span>
                                                <code>{{ substr(config('services.stripe.key'), 0, 20) }}...</code>
                                            @else
                                                <span class="badge badge-danger">❌ Non</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Mode :</strong></td>
                                        <td>
                                            @if(str_contains(config('services.stripe.key'), 'test'))
                                                <span class="badge badge-info">TEST</span>
                                            @else
                                                <span class="badge badge-warning">PRODUCTION</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Utilisateur connecté :</strong></td>
                                        <td>{{ auth()->user()->name ?? 'Non connecté' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
