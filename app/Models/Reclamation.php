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
     * Une réclamation peut avoir plusieurs avis
     */
    public function avis()
    {
        return $this->hasMany(Avis::class);
    }
}
