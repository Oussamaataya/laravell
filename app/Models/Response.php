<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'reclamation_id',
        'user_id',
        'contenu',
    ];

    /**
     * Une réponse appartient à une réclamation
     */
    public function reclamation()
    {
        return $this->belongsTo(Reclamation::class);
    }

    /**
     * Une réponse appartient à un utilisateur (admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
