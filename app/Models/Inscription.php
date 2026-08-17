<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Inscription extends Model
{
    use HasFactory;

    /**
     * Libellés d'affichage des statuts, centralisés ici pour ne plus être
     * dupliqués dans chaque vue.
     */
    public const STATUTS = [
        'pending' => 'En attente',
        'paid' => 'Payée',
        'cancelled' => 'Annulée',
        'expired' => 'Expirée',
    ];

    protected $fillable = [
        'edition_id', 'participant_id', 'region_id', 'profil_id',
        'code_inscription', 'statut', 'montant', 'paid_at',
        'jours_participation', 'besoins_particuliers', 'source_connaissance',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'jours_participation' => 'array',
        'montant' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Inscription $inscription) {
            if (empty($inscription->code_inscription)) {
                $inscription->code_inscription = self::genererCodeUnique();
            }
        });
    }

    public static function genererCodeUnique(): string
    {
        do {
            // ex: FLM-7K3P9Q
            $code = 'FLM-'.Str::upper(Str::random(6));
        } while (self::where('code_inscription', $code)->exists());

        return $code;
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function paiementReussi(): ?Paiement
    {
        return $this->paiements()->where('statut', 'completed')->latest()->first();
    }

    public function recu(): HasOne
    {
        return $this->hasOne(Recu::class);
    }

    public function estPayee(): bool
    {
        return $this->statut === 'paid';
    }

    public function marquerCommePayee(): void
    {
        $this->update(['statut' => 'paid', 'paid_at' => now()]);
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * Type de badge à utiliser dans le back-office pour ce statut.
     */
    public function badgeStatut(): string
    {
        return match ($this->statut) {
            'paid' => 'succes',
            'pending' => 'attente',
            'cancelled', 'expired' => 'erreur',
            default => 'neutre',
        };
    }

    /**
     * Jours de présence formatés pour l'affichage et l'export.
     */
    public function joursFormates(): string
    {
        $jours = collect($this->jours_participation ?? [])
            ->map(fn ($jour) => \Illuminate\Support\Carbon::parse($jour)->translatedFormat('D d M'))
            ->all();

        return empty($jours) ? 'Tous les jours' : implode(', ', $jours);
    }

    public function scopePayees(Builder $query): Builder
    {
        return $query->where('statut', 'paid');
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', 'pending');
    }
}
