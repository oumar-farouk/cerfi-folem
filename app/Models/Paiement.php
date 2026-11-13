<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory;

    public const STATUTS = [
        'pending' => 'En attente',
        'completed' => 'Réussi',
        'failed' => 'Échoué',
        'cancelled' => 'Annulé',
    ];

    public const MODES = [
        'ligdicash' => 'Mobile money (LigdiCash)',
        'especes' => 'Espèces',
        'virement' => 'Virement',
        'cheque' => 'Chèque',
        'autre' => 'Autre',
    ];

    /** Modes saisissables à la main par l'équipe. */
    public const MODES_MANUELS = ['especes', 'virement', 'cheque', 'autre'];

    protected $fillable = [
        'inscription_id', 'mode', 'operateur', 'token_transaction', 'reference_ligdicash',
        'reference_manuelle', 'statut', 'montant', 'devise', 'note', 'enregistre_par',
        'payload_creation', 'payload_callback', 'payload_confirmation', 'confirmed_at',
    ];

    protected $casts = [
        'payload_creation' => 'array',
        'payload_callback' => 'array',
        'payload_confirmation' => 'array',
        'confirmed_at' => 'datetime',
        'montant' => 'integer',
    ];

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    public function estManuel(): bool
    {
        return $this->mode !== 'ligdicash';
    }

    public function libelleMode(): string
    {
        return self::MODES[$this->mode] ?? $this->mode;
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * Filtres communs à l'écran, aux compteurs et à l'export, pour que le fichier
     * téléchargé porte exactement sur ce que l'écran affiche.
     *
     * @param  array{edition_id?: int|null, statut?: string|null, mode?: string|null, q?: string|null, du?: string|null, au?: string|null}  $f
     */
    public function scopeFiltres(Builder $query, array $f): Builder
    {
        return $query
            ->when(! empty($f['edition_id']), fn (Builder $q) => $q->whereHas(
                'inscription', fn (Builder $i) => $i->where('edition_id', $f['edition_id'])
            ))
            // Le paramètre vient de l'URL : on n'accepte que des valeurs connues.
            ->when(! empty($f['statut']) && array_key_exists($f['statut'], self::STATUTS),
                fn (Builder $q) => $q->where('statut', $f['statut']))
            ->when(! empty($f['mode']) && array_key_exists($f['mode'], self::MODES),
                fn (Builder $q) => $q->where('mode', $f['mode']))
            ->when(! empty($f['q']), function (Builder $q) use ($f) {
                $r = $f['q'];
                $q->where(function (Builder $s) use ($r) {
                    $s->where('reference_ligdicash', 'like', "%{$r}%")
                        ->orWhere('reference_manuelle', 'like', "%{$r}%")
                        ->orWhereHas('inscription', function (Builder $i) use ($r) {
                            $i->where('code_inscription', 'like', "%{$r}%")
                                ->orWhereHas('participant', fn (Builder $p) => $p
                                    ->where('nom', 'like', "%{$r}%")
                                    ->orWhere('prenom', 'like', "%{$r}%")
                                    ->orWhere('telephone', 'like', "%{$r}%"));
                        });
                });
            })
            ->when(! empty($f['du']), fn (Builder $q) => $q->whereDate('created_at', '>=', $f['du']))
            ->when(! empty($f['au']), fn (Builder $q) => $q->whereDate('created_at', '<=', $f['au']));
    }
}
