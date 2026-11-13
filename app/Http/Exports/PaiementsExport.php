<?php

namespace App\Exports;

use App\Models\Paiement;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaiementsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(protected array $filtres) {}

    public function query(): Builder
    {
        return Paiement::query()
            ->filtres($this->filtres)
            ->with(['inscription.participant', 'enregistrePar'])
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'Date', 'Code inscription', 'Participant', 'Téléphone', 'Mode', 'Opérateur',
            'Référence', 'Montant (FCFA)', 'Statut', 'Enregistré par', 'Note',
        ];
    }

    /** @param  Paiement  $p */
    public function map($p): array
    {
        return [
            $p->created_at->format('d/m/Y H:i'),
            $p->inscription->code_inscription,
            $p->inscription->participant->nom_complet,
            $p->inscription->participant->telephone,
            $p->libelleMode(),
            $p->operateur,
            $p->reference_manuelle ?? $p->reference_ligdicash,
            $p->montant,
            $p->libelleStatut(),
            $p->enregistrePar?->name,
            $p->note,
        ];
    }
}
