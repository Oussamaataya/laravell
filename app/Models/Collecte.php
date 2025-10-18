<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collecte extends Model
{
    protected $fillable = [
        'montant', 
        'methode_paiement', 
        'statut', 
        'campagne_id', 
        'utilisateur_id',
        'stripe_session_id',
        'stripe_payment_intent',
        'message',
    ];

    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'campagne_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}

