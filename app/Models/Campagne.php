<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campagne extends Model
{
    protected $table = 'compagnes';

    protected $fillable = [
        'nom', 'description', 'montant_objectif', 'montant_actuel',
        'date_debut', 'date_fin', 'statut', 'organisateur_id'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant_objectif' => 'decimal:2',
        'montant_actuel' => 'decimal:2',
    ];

    public function collectes()
    {
        return $this->hasMany(Collecte::class, 'campagne_id');
    }

    public function organisateur()
    {
        return $this->belongsTo(User::class, 'organisateur_id');
    }
}

