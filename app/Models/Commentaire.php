<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected $fillable = [
        'contenu',
        'publication_id',
        'user_id',
        'tone',
        'has_bad_words',
        'bad_words'
    ];

    protected $casts = [
        'has_bad_words' => 'boolean',
        'bad_words' => 'array',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
