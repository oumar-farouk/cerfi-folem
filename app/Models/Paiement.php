<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id', 'operateur', 'token_transaction', 'reference_ligdicash',
        'statut', 'montant', 'devise', 'payload_creation', 'payload_callback',
        'payload_confirmation', 'confirmed_at',
    ];

    protected $casts = [
        'payload_creation' => 'array',
        'payload_callback' => 'array',
        'payload_confirmation' => 'array',
        'confirmed_at' => 'datetime',
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }
}
