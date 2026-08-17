<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partenaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'edition_id', 'nom', 'logo', 'type', 'niveau', 'lien_site', 'ordre',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}
