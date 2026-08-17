<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recu extends Model
{
    use HasFactory;

    protected $table = 'recus';

    protected $fillable = [
        'inscription_id', 'numero_recu', 'chemin_pdf', 'hash_verification', 'genere_le',
    ];

    protected $casts = [
        'genere_le' => 'datetime',
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }
}
