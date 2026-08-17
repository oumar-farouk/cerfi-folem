<?php

namespace App\Exports;

use App\Models\Inscription;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InscriptionsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected ?int $editionId = null,
        protected ?string $statut = null,
        protected ?string $recherche = null,
    ) {}

    public function query(): Builder
    {
        return Inscription::query()
            ->when($this->editionId, fn (Builder $q) => $q->where('edition_id', $this->editionId))
            ->when(
                $this->statut && array_key_exists($this->statut, Inscription::STATUTS),
                fn (Builder $q) => $q->where('statut', $this->statut)
            )
            ->when($this->recherche, function (Builder $q) {
                $terme = $this->recherche;

                $q->where(function (Builder $sous) use ($terme) {
                    $sous->where('code_inscription', 'like', "%{$terme}%")
                        ->orWhereHas('participant', function (Builder $p) use ($terme) {
                            $p->where('nom', 'like', "%{$terme}%")
                                ->orWhere('prenom', 'like', "%{$terme}%")
                                ->orWhere('email', 'like', "%{$terme}%")
                                ->orWhere('telephone', 'like', "%{$terme}%")
                                ->orWhere('structure', 'like', "%{$terme}%");
                        });
                });
            })
            ->with(['participant', 'edition', 'region', 'profil'])
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Code inscription', 'Édition', 'Nom', 'Prénom', 'Email', 'Téléphone',
            'Structure', 'Fonction', 'Secteur', 'Région', 'Profil',
            'Jours de participation', 'Besoins particuliers', 'Connu via',
            'Statut', 'Montant (FCFA)', 'Date de paiement', 'Date d\'inscription',
        ];
    }

    /**
     * @param  Inscription  $inscription
     */
    public function map($inscription): array
    {
        return [
            $inscription->code_inscription,
            $inscription->edition?->nom,
            $inscription->participant?->nom,
            $inscription->participant?->prenom,
            $inscription->participant?->email,
            $inscription->participant?->telephone,
            $inscription->participant?->structure,
            $inscription->participant?->fonction,
            $inscription->participant?->secteur_activite,
            $inscription->region?->nom,
            $inscription->profil?->nom,
            $inscription->joursFormates(),
            $inscription->besoins_particuliers,
            $inscription->source_connaissance,
            $inscription->libelleStatut(),
            $inscription->montant,
            $inscription->paid_at?->format('d/m/Y H:i'),
            $inscription->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
