<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'commentaire',
        'user_id',
        'reclamation_id',
    ];

    /**
     * Un avis appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un avis appartient à une réclamation
     */
    public function reclamation()
    {
        return $this->belongsTo(Reclamation::class);
    }
}
