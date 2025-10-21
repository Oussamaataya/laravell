<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    /**
     * Créer un PaymentIntent pour un don
     */
    public function createPaymentIntent($amount, $currency = 'eur', $metadata = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Stripe utilise les centimes
                'currency' => $currency,
                'metadata' => $metadata,
                'payment_method_types' => ['card'],
            ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            Log::error('Erreur création PaymentIntent: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirmer un paiement
     */
    public function confirmPayment($paymentIntentId, $paymentMethodId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->confirm([
                'payment_method' => $paymentMethodId,
            ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            Log::error('Erreur confirmation paiement: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer tous les paiements (charges) du compte Stripe
     */
    public function getAllCharges($limit = 100)
    {
        try {
            $charges = Charge::all([
                'limit' => $limit,
            ]);

            return $charges->data;
        } catch (\Exception $e) {
            Log::error('Erreur récupération charges: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les charges avec pagination
     */
    public function getChargesPaginated($startingAfter = null, $limit = 100)
    {
        try {
            $params = ['limit' => $limit];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            $charges = Charge::all($params);
            return $charges;
        } catch (\Exception $e) {
            Log::error('Erreur récupération charges paginées: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculer le total des paiements réussis
     */
    public function getTotalPaidAmount()
    {
        try {
            $charges = $this->getAllCharges(1000); // Récupérer beaucoup de charges
            $total = 0;

            foreach ($charges as $charge) {
                if ($charge->status === 'succeeded') {
                    $total += $charge->amount;
                }
            }

            return $total / 100; // Convertir en euros
        } catch (\Exception $e) {
            Log::error('Erreur calcul total: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les détails d'un paiement spécifique
     */
    public function getCharge($chargeId)
    {
        try {
            return Charge::retrieve($chargeId);
        } catch (\Exception $e) {
            Log::error('Erreur récupération charge: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les statistiques des paiements
     */
    public function getPaymentStats()
    {
        try {
            $charges = $this->getAllCharges(1000);
            $stats = [
                'total_amount' => 0,
                'total_charges' => 0,
                'successful_charges' => 0,
                'failed_charges' => 0,
                'refunded_amount' => 0,
            ];

            foreach ($charges as $charge) {
                $stats['total_charges']++;

                if ($charge->status === 'succeeded') {
                    $stats['successful_charges']++;
                    $stats['total_amount'] += $charge->amount;
                } elseif ($charge->status === 'failed') {
                    $stats['failed_charges']++;
                }

                if ($charge->amount_refunded > 0) {
                    $stats['refunded_amount'] += $charge->amount_refunded;
                }
            }

            // Convertir les montants en euros
            $stats['total_amount'] /= 100;
            $stats['refunded_amount'] /= 100;

            return $stats;
        } catch (\Exception $e) {
            Log::error('Erreur récupération stats: ' . $e->getMessage());
            return null;
        }
    }
}
