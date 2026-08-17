<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Edition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RolesSeeder::class,
            ParametrageSeeder::class,
        ]);

        // Éditions historiques + l'édition active pour tester le parcours complet
        Edition::updateOrCreate(['slug' => 'folem-2022'], [
            'nom' => 'FOLEM 2022', 'annee' => 2022,
            'theme' => 'Première édition',
            'date_debut' => '2022-10-29', 'date_fin' => '2022-10-31',
            'lieu' => 'Ouagadougou', 'statut' => 'archived', 'montant_inscription' => 10000,
        ]);

        Edition::updateOrCreate(['slug' => 'folem-2024'], [
            'nom' => 'FOLEM 2024', 'annee' => 2024,
            'theme' => 'Entrepreneuriat Éthique et Innovant pour Burkina résilient',
            'date_debut' => '2024-10-31', 'date_fin' => '2024-11-02',
            'lieu' => 'Ouagadougou', 'statut' => 'archived', 'montant_inscription' => 10000,
        ]);

        Edition::updateOrCreate(['slug' => 'folem-2026'], [
            'nom' => 'FOLEM 2026', 'annee' => 2026,
            'theme' => 'Entrepreneuriat éthique et innovant pour un Burkina résilient',
            'date_debut' => now()->addMonths(2), 'date_fin' => now()->addMonths(2)->addDays(2),
            'lieu' => 'Ouagadougou', 'statut' => 'active', 'montant_inscription' => 15000,
        ]);
    }
}
