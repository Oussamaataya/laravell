<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $table = 'reclamations';

    protected $fillable = [
        'sujet',
        'description',
        'statut',
        'user_id',
    ];

    /**
     * Une réclamation appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une réclamation peut avoir plusieurs réponses
     */
    public function responses()
    {
        return $this->hasMany(Response::class)->orderBy('created_at', 'asc');
    }

    /**
     * Une réclamation peut avoir plusieurs avis
     */
    public function avis()
    {
        return $this->hasMany(Avis::class);
    }

    /**
     * Vérifier si la réclamation a des réponses
     */
    public function hasResponses(): bool
    {
        return $this->responses()->exists();
    }

    /**
     * Obtenir la note moyenne des avis
     */
    public function averageRating(): float
    {
        return $this->avis()->avg('note') ?? 0;
    }
}
