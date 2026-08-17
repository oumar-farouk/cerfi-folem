<?php

namespace Database\Seeders;

use App\Models\Profil;
use App\Models\Region;
use Illuminate\Database\Seeder;

/**
 * Paramétrage de base partagé entre les éditions : les treize régions
 * administratives du Burkina Faso, plus une entrée « Diaspora », et les
 * profils de participation utilisés par la grille tarifaire.
 */
class ParametrageSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'Boucle du Mouhoun', 'Cascades', 'Centre', 'Centre-Est', 'Centre-Nord',
            'Centre-Ouest', 'Centre-Sud', 'Est', 'Hauts-Bassins', 'Nord',
            'Plateau-Central', 'Sahel', 'Sud-Ouest', 'Diaspora',
        ];

        foreach ($regions as $ordre => $nom) {
            Region::updateOrCreate(['nom' => $nom], ['ordre' => $ordre]);
        }

        $profils = [
            'Étudiant',
            'Jeune entrepreneur',
            'Entrepreneur confirmé',
            'Professionnel',
            'Institution / Partenaire',
        ];

        foreach ($profils as $ordre => $nom) {
            Profil::updateOrCreate(['nom' => $nom], ['ordre' => $ordre]);
        }
    }
}
