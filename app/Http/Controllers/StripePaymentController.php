<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Collecte;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        // Configurer Stripe avec la clé secrète
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Créer une session de paiement Stripe Checkout
     */
    public function createCheckoutSession(Request $request, Campagne $campagne)
    {
        $request->validate([
            'montant' => 'required|numeric|min:1|max:' . $campagne->montant_objectif,
            'message' => 'nullable|string|max:255',
        ]);

        try {
            Log::info('Début création session Stripe', [
                'montant' => $request->montant,
                'campagne' => $campagne->nom,
                'user_id' => auth()->id(),
            ]);

            // Créer d'abord la collecte en statut "en_attente"
            $collecte = Collecte::create([
                'montant' => $request->montant,
                'methode_paiement' => 'stripe',
                'statut' => 'en_attente',
                'campagne_id' => $campagne->id,
                'utilisateur_id' => auth()->id(),
                'message' => $request->message,
            ]);

            Log::info('Collecte créée', ['collecte_id' => $collecte->id]);

            // Créer une session Stripe Checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Don pour: ' . $campagne->nom,
                            'description' => $campagne->description ? substr($campagne->description, 0, 200) : 'Soutien à la campagne',
                        ],
                        'unit_amount' => (int)($request->montant * 100), // Stripe utilise les centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['collecte' => $collecte->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', ['collecte' => $collecte->id]),
                'customer_email' => auth()->user()->email,
                'metadata' => [
                    'collecte_id' => (string)$collecte->id,
                    'campagne_id' => (string)$campagne->id,
                    'user_id' => (string)auth()->id(),
                    'campagne_nom' => $campagne->nom,
                ],
            ]);

            Log::info('Session Stripe créée', [
                'session_id' => $session->id,
                'url' => $session->url,
            ]);

            // Stocker le session ID pour référence
            $collecte->update(['stripe_session_id' => $session->id]);

            // Rediriger vers Stripe Checkout
            Log::info('Redirection vers Stripe Checkout', ['url' => $session->url]);
            
            return redirect()->away($session->url);

        } catch (\Exception $e) {
            Log::error('Erreur création session Stripe: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Erreur lors de la création du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Gérer le succès du paiement
     */
    public function success(Request $request, Collecte $collecte)
    {
        try {
            $sessionId = $request->query('session_id');
            
            if (!$sessionId) {
                return redirect()->route('collectes.donate.form', $collecte->campagne)
                    ->with('error', 'Session de paiement invalide.');
            }

            // Récupérer les détails de la session Stripe
            $session = Session::retrieve($sessionId);

            // Vérifier que le paiement est bien complété
            if ($session->payment_status === 'paid') {
                // Mettre à jour la collecte
                $collecte->update([
                    'statut' => 'validé',
                    'stripe_payment_intent' => $session->payment_intent,
                ]);

                // Mettre à jour le montant actuel de la campagne
                $collecte->campagne->increment('montant_actuel', $collecte->montant);

                Log::info('Paiement Stripe réussi', [
                    'collecte_id' => $collecte->id,
                    'montant' => $collecte->montant,
                    'session_id' => $sessionId,
                    'payment_intent' => $session->payment_intent,
                ]);

                return redirect()->route('collectes.donate.form', $collecte->campagne)
                    ->with('success', '🎉 Merci ! Votre don de ' . number_format($collecte->montant, 2) . ' € a été enregistré avec succès !');
            }

            return redirect()->route('collectes.donate.form', $collecte->campagne)
                ->with('warning', 'Le paiement n\'a pas encore été confirmé.');

        } catch (\Exception $e) {
            Log::error('Erreur traitement succès Stripe: ' . $e->getMessage());
            
            return redirect()->route('collectes.donate.form', $collecte->campagne)
                ->with('error', 'Erreur lors de la validation du paiement.');
        }
    }

    /**
     * Gérer l'annulation du paiement
     */
    public function cancel(Collecte $collecte)
    {
        // Marquer la collecte comme échouée
        $collecte->update(['statut' => 'échoué']);

        Log::info('Paiement Stripe annulé', [
            'collecte_id' => $collecte->id,
            'montant' => $collecte->montant,
        ]);

        return redirect()->route('collectes.donate.form', $collecte->campagne)
            ->with('info', 'Le paiement a été annulé. Vous pouvez réessayer quand vous le souhaitez.');
    }

    /**
     * Webhook Stripe pour les événements
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            // Gérer différents types d'événements
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleCheckoutSessionCompleted($session);
                    break;

                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    Log::info('Payment Intent réussi: ' . $paymentIntent->id);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $this->handlePaymentFailed($paymentIntent);
                    break;

                default:
                    Log::info('Événement Stripe non géré: ' . $event->type);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Erreur webhook Stripe: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Gérer la complétion de la session checkout
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $collecteId = $session->metadata->collecte_id ?? null;

        if ($collecteId) {
            $collecte = Collecte::find($collecteId);
            
            if ($collecte && $collecte->statut === 'en_attente') {
                $collecte->update([
                    'statut' => 'validé',
                    'stripe_payment_intent' => $session->payment_intent,
                ]);

                $collecte->campagne->increment('montant_actuel', $collecte->montant);

                Log::info('Collecte validée via webhook', [
                    'collecte_id' => $collecte->id,
                    'montant' => $collecte->montant,
                ]);
            }
        }
    }

    /**
     * Gérer l'échec du paiement
     */
    private function handlePaymentFailed($paymentIntent)
    {
        // Trouver la collecte associée
        $collecte = Collecte::where('stripe_payment_intent', $paymentIntent->id)->first();

        if ($collecte) {
            $collecte->update(['statut' => 'échoué']);

            Log::warning('Paiement échoué', [
                'collecte_id' => $collecte->id,
                'payment_intent' => $paymentIntent->id,
            ]);
        }
    }
}
