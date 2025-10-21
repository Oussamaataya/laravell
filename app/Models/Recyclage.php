<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recyclage extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'lieu',
        'date_collecte',
        'heure_debut',
        'heure_fin',
        'quantite_prevue',
        'quantite_collectee',
        'statut',
        'notes',
        'type_recyclage_id',
        'user_id'
    ];

    protected $casts = [
        'date_collecte' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'quantite_prevue' => 'decimal:2',
        'quantite_collectee' => 'decimal:2'
    ];

    /**
     * Relation avec le type de recyclage
     */
    public function typeRecyclage()
    {
        return $this->belongsTo(TypeRecyclage::class);
    }

    /**
     * Relation avec l'utilisateur organisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les recyclages à venir
     */
    public function scopeAvenir($query)
    {
        return $query->where('date_collecte', '>=', Carbon::today());
    }

    /**
     * Scope pour les recyclages par statut
     */
    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Accesseur pour le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        $statuts = [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }
 public function getStatutFormateAttribute2()
    {
        $statuts = [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }


}
