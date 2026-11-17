<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Inscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JySeraiController extends Controller
{
    /**
     * Chemin (sous public/) du modèle d'affiche fourni par le comité.
     * Dates, lieu, thème et coordonnées sont DANS cette image : pour une
     * nouvelle édition, on remplace simplement le fichier.
     */
    protected const MODELE = 'img/jyserai/folem-cadre.jpeg';

    /**
     * Page publique « J'y serai » : le visiteur crée son affiche personnalisée.
     * La photo est traitée uniquement dans le navigateur (canvas) : rien n'est
     * envoyé ni stocké côté serveur.
     */
    public function __invoke(Request $request, Edition $edition): View
    {
        $dates = $this->dates($edition);
        $lien = route('inscription.form', $edition);

        $texte = collect([
            "Je serai au {$edition->nom} !",
            $dates ? "📅 {$dates}" : null,
            $edition->lieu ? "📍 {$edition->lieu}" : null,
            '',
            "Rejoignez-moi, inscrivez-vous ici : {$lien}",
        ])->filter(fn ($ligne) => $ligne !== null)->implode("\n");

        return view('site.jyserai', [
            'edition' => $edition,
            'config' => [
                'cadre' => is_file(public_path(self::MODELE)) ? asset(self::MODELE) : null,
                'edition' => $edition->nom,
                'texte' => $texte,
                'prefill' => $this->prefill($request, $edition),
            ],
        ]);
    }

    /**
     * Pré-remplit nom et fonction pour une personne déjà inscrite à cette édition :
     *  1. via un lien SIGNÉ généré par nos pages (dossier, notification de paiement) ;
     *     la signature empêche de forger un lien pour lire les infos d'un autre code ;
     *  2. à défaut, via le compte connecté si son participant a une inscription active.
     * Sans l'un ni l'autre, le formulaire reste vide : aucune donnée n'est exposée.
     *
     * @return array{nom: string, role: string}|null
     */
    protected function prefill(Request $request, Edition $edition): ?array
    {
        $inscription = null;

        if ($request->filled('code') && $request->hasValidSignature()) {
            $inscription = Inscription::with('participant')
                ->where('edition_id', $edition->id)
                ->where('code_inscription', $request->string('code')->toString())
                ->first();
        }

        if (! $inscription && $request->user()) {
            $inscription = Inscription::with('participant')
                ->where('edition_id', $edition->id)
                ->whereIn('statut', ['pending', 'paid'])
                ->whereHas('participant', fn ($q) => $q->where('user_id', $request->user()->id))
                ->first();
        }

        $participant = $inscription?->participant;

        if (! $participant) {
            return null;
        }

        return [
            'nom' => Str::limit($participant->nom_complet, 40, ''),
            'role' => Str::limit(
                collect([$participant->fonction, $participant->structure])->filter()->implode(' · '),
                60,
                ''
            ),
        ];
    }

    protected function dates(Edition $edition): string
    {
        $debut = $edition->date_debut;
        $fin = $edition->date_fin;

        if (! $debut) {
            return '';
        }

        if (! $fin || $debut->isSameDay($fin)) {
            return $debut->translatedFormat('j F Y');
        }

        if ($debut->format('Y-m') === $fin->format('Y-m')) {
            return "Du {$debut->day} au ".$fin->translatedFormat('j F Y');
        }

        $formatDebut = $debut->year === $fin->year ? 'j F' : 'j F Y';

        return 'Du '.$debut->translatedFormat($formatDebut).' au '.$fin->translatedFormat('j F Y');
    }
}
