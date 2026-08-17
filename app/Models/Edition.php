<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Edition extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'nom', 'slug', 'theme', 'annee', 'date_debut', 'date_fin',
        'lieu', 'description', 'cover_image', 'statut', 'montant_inscription',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Edition $edition) {
            if (empty($edition->slug)) {
                $edition->slug = Str::slug($edition->nom.'-'.$edition->annee);
            }
        });

        // Garantit qu'une seule édition est "active" à la fois
        static::saving(function (Edition $edition) {
            if ($edition->statut === 'active') {
                static::where('id', '!=', $edition->id)
                    ->where('statut', 'active')
                    ->update(['statut' => 'archived']);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('galerie');
        $this->addMediaCollection('cover')->singleFile();
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class)->orderBy('date')->orderBy('heure_debut');
    }

    public function intervenants(): HasMany
    {
        return $this->hasMany(Intervenant::class);
    }

    public function partenaires(): HasMany
    {
        return $this->hasMany(Partenaire::class)->orderBy('ordre');
    }

    public function paysAttendus(): HasMany
    {
        return $this->hasMany(PaysAttendu::class);
    }

    public function prix(): HasMany
    {
        return $this->hasMany(Prix::class);
    }

    /**
     * Retrouve le tarif applicable pour une combinaison région + profil sur cette édition.
     * Retourne null si aucun tarif n'a été paramétré pour cette combinaison.
     */
    public function tarifPour(int $regionId, int $profilId): ?int
    {
        return $this->prix()
            ->where('region_id', $regionId)
            ->where('profil_id', $profilId)
            ->value('montant');
    }

    /**
     * Tarif le plus bas de la grille, pour l'affichage "à partir de X FCFA" sur les pages publiques.
     */
    public function tarifMinimum(): ?int
    {
        return $this->prix()->min('montant');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('statut', 'active');
    }

    public function scopeArchivees(Builder $query): Builder
    {
        return $query->where('statut', 'archived')->orderByDesc('annee');
    }

    public function estOuverte(): bool
    {
        return $this->statut === 'active' && now()->lte($this->date_fin);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
