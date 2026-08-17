<?php

namespace App\Services;

use App\Models\Inscription;
use App\Models\Recu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecuService
{
    public function genererPour(Inscription $inscription): Recu
    {
        if ($inscription->recu()->exists()) {
            return $inscription->recu;
        }

        $numero = sprintf(
            '%s-%06d',
            Str::upper($inscription->edition->slug),
            $inscription->id
        );

        $hash = hash('sha1', $inscription->code_inscription.$inscription->id.config('app.key'));

        $pdf = Pdf::loadView('receipts.recu', [
            'inscription' => $inscription,
            'participant' => $inscription->participant,
            'edition' => $inscription->edition,
            'numero' => $numero,
            'hash' => $hash,
        ]);

        $chemin = "recus/{$inscription->edition->slug}/{$numero}.pdf";
        Storage::disk('public')->put($chemin, $pdf->output());

        return Recu::create([
            'inscription_id' => $inscription->id,
            'numero_recu' => $numero,
            'chemin_pdf' => $chemin,
            'hash_verification' => $hash,
            'genere_le' => now(),
        ]);
    }
}
