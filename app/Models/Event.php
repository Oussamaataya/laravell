<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'max_participants',
        'current_participants',
        'price',
        'is_free',
        'is_online',
        'meeting_link',
        'category',
        'eco_impact',
        'carbon_footprint',
        'sustainability_score',
        'image',
        'gallery',
        'organizer_name',
        'organizer_email',
        'organizer_phone',
        'status',
        'is_featured',
        'registration_deadline',
        'requirements',
        'what_to_bring',
        'accessibility_info',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'datetime',
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'carbon_footprint' => 'decimal:2',
        'sustainability_score' => 'integer',
        'is_free' => 'boolean',
        'is_online' => 'boolean',
        'is_featured' => 'boolean',
        'gallery' => 'array',
        'requirements' => 'array',
        'what_to_bring' => 'array',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'registration_deadline',
        'deleted_at',
    ];

    // Relations
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', '%' . $city . '%');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return $this->is_free ? 'Gratuit' : number_format($this->price, 2) . ' €';
    }

    public function getFullDateAttribute()
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d/m/Y') . ' de ' . 
                   $this->start_time . ' à ' . 
                   $this->end_time;
        }
        
        return 'Du ' . $this->start_date->format('d/m/Y') . ' au ' . 
               $this->end_date->format('d/m/Y');
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        
        return $this->max_participants - $this->current_participants;
    }

    public function getIsFullAttribute()
    {
        if (!$this->max_participants) {
            return false;
        }
        
        return $this->current_participants >= $this->max_participants;
    }

    public function getRegistrationStatusAttribute()
    {
        if ($this->registration_deadline && now() > $this->registration_deadline) {
            return 'closed';
        }
        
        if ($this->is_full) {
            return 'full';
        }
        
        return 'open';
    }

    public function getEcoImpactBadgeAttribute()
    {
        $score = $this->sustainability_score;
        
        if ($score >= 80) {
            return ['label' => 'Très Éco-responsable', 'class' => 'success'];
        } elseif ($score >= 60) {
            return ['label' => 'Éco-responsable', 'class' => 'primary'];
        } elseif ($score >= 40) {
            return ['label' => 'Moyennement Éco', 'class' => 'warning'];
        } else {
            return ['label' => 'Peu Éco', 'class' => 'secondary'];
        }
    }

    // Mutators
    public function setGalleryAttribute($value)
    {
        $this->attributes['gallery'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setRequirementsAttribute($value)
    {
        $this->attributes['requirements'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setWhatToBringAttribute($value)
    {
        $this->attributes['what_to_bring'] = is_array($value) ? json_encode($value) : $value;
    }

    // Methods
    public function canRegister()
    {
        return $this->status === 'active' && 
               $this->registration_status === 'open' &&
               $this->start_date >= now()->toDateString();
    }

    public function incrementParticipants()
    {
        $this->increment('current_participants');
    }

    public function decrementParticipants()
    {
        $this->decrement('current_participants');
    }

    public static function getCategories()
    {
        return [
            'nettoyage' => 'Nettoyage Environnemental',
            'plantation' => 'Plantation & Jardinage',
            'recyclage' => 'Recyclage & Upcycling',
            'education' => 'Éducation Environnementale',
            'energie' => 'Énergies Renouvelables',
            'transport' => 'Transport Durable',
            'alimentation' => 'Alimentation Durable',
            'biodiversite' => 'Protection de la Biodiversité',
            'climat' => 'Action Climatique',
            'autre' => 'Autre',
        ];
    }

    public static function getStatuses()
    {
        return [
            'draft' => 'Brouillon',
            'active' => 'Actif',
            'cancelled' => 'Annulé',
            'completed' => 'Terminé',
        ];
    }
}
