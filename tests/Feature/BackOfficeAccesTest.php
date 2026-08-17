<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackOfficeAccesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_anonyme_est_renvoye_vers_la_connexion(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_un_utilisateur_sans_role_ne_peut_pas_entrer(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_un_gestionnaire_accede_au_tableau_de_bord(): void
    {
        Role::findOrCreate('gestionnaire');

        $utilisateur = User::factory()->create();
        $utilisateur->assignRole('gestionnaire');

        $this->actingAs($utilisateur)
            ->get('/admin')
            ->assertOk();
    }

    public function test_les_en_tetes_de_securite_sont_presents(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
