<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Intervenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'edition_id', 'nom', 'fonction', 'structure', 'bio', 'photo', 'linkedin_url',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function programmes(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class, 'intervenant_programme');
    }
}
