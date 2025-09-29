<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeRecyclage extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'couleur',
        'icone',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean'
    ];

    /**
     * Relation avec les recyclages
     */
    public function recyclages()
    {
        return $this->hasMany(Recyclage::class);
    }

    /**
     * Scope pour les types actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
