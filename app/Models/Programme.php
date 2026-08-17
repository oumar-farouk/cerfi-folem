<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'edition_id', 'titre', 'description', 'type', 'date',
        'heure_debut', 'heure_fin', 'salle', 'ordre',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function intervenants(): BelongsToMany
    {
        return $this->belongsToMany(Intervenant::class, 'intervenant_programme');
    }
}
