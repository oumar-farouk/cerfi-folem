<?php

namespace Tests\Feature;

use App\Livewire\Registration\RegisterForm;
use App\Models\Edition;
use App\Models\Inscription;
use App\Models\Prix;
use App\Models\Profil;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InscriptionPubliqueTest extends TestCase
{
    use RefreshDatabase;

    protected Edition $edition;

    protected Region $region;

    protected Profil $profil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->edition = Edition::create([
            'nom' => 'FOLEM Test',
            'annee' => (int) now()->addMonth()->format('Y'),
            'theme' => 'Thème de test',
            'date_debut' => now()->addMonth()->toDateString(),
            'date_fin' => now()->addMonth()->addDays(2)->toDateString(),
            'lieu' => 'Ouagadougou',
            'statut' => 'active',
        ]);

        $this->region = Region::create(['nom' => 'Centre', 'ordre' => 0]);
        $this->profil = Profil::create(['nom' => 'Étudiant', 'ordre' => 0]);

        Prix::create([
            'edition_id' => $this->edition->id,
            'region_id' => $this->region->id,
            'profil_id' => $this->profil->id,
            'montant' => 10000,
        ]);
    }

    public function test_la_page_d_accueil_affiche_l_edition_active(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('FOLEM Test');
    }

    public function test_une_edition_en_brouillon_n_est_pas_consultable(): void
    {
        $brouillon = Edition::create([
            'nom' => 'FOLEM Brouillon',
            'annee' => 2030,
            'date_debut' => '2030-01-01',
            'date_fin' => '2030-01-03',
            'statut' => 'draft',
        ]);

        $this->get(route('editions.show', $brouillon))->assertNotFound();
    }

    public function test_le_parcours_complet_cree_une_inscription_en_attente(): void
    {
        $composant = Livewire::test(RegisterForm::class, ['edition' => $this->edition])
            ->set('prenom', 'Aminata')
            ->set('nom', 'Ouédraogo')
            ->set('email', 'aminata@example.test')
            ->set('telephone', '22670000000')
            ->set('secteur_activite', 'Secteur Privé')
            ->call('etapeSuivante')
            ->assertSet('etape', 2)
            ->set('region_id', $this->region->id)
            ->set('profil_id', $this->profil->id)
            ->set('source_connaissance', 'Réseaux sociaux')
            ->call('etapeSuivante')
            ->assertSet('etape', 3)
            ->set('accepte_conditions', true)
            ->call('submit')
            ->assertSet('inscriptionTerminee', true);

        $inscription = Inscription::first();

        $this->assertNotNull($inscription);
        $this->assertSame('pending', $inscription->statut);
        $this->assertSame(10000, $inscription->montant);
        $this->assertStringStartsWith('FLM-', $inscription->code_inscription);
        $this->assertCount(3, $inscription->jours_participation);
        $composant->assertSet('codeGenere', $inscription->code_inscription);
    }

    public function test_le_montant_vient_de_la_base_et_non_du_navigateur(): void
    {
        Livewire::test(RegisterForm::class, ['edition' => $this->edition])
            ->set('prenom', 'Issa')
            ->set('nom', 'Sawadogo')
            ->set('email', 'issa@example.test')
            ->set('telephone', '22670000001')
            ->set('secteur_activite', 'Entrepreneur')
            ->set('region_id', $this->region->id)
            ->set('profil_id', $this->profil->id)
            ->set('accepte_conditions', true)
            // Tentative de forcer un montant : la propriété n'existe pas côté composant.
            ->set('etape', 3)
            ->call('submit');

        $this->assertSame(10000, Inscription::first()->montant);
    }

    public function test_un_numero_de_telephone_mal_forme_bloque_la_premiere_etape(): void
    {
        Livewire::test(RegisterForm::class, ['edition' => $this->edition])
            ->set('prenom', 'Fatou')
            ->set('nom', 'Kaboré')
            ->set('email', 'fatou@example.test')
            ->set('telephone', '0022670000000')
            ->set('secteur_activite', 'ONG')
            ->call('etapeSuivante')
            ->assertHasErrors('telephone')
            ->assertSet('etape', 1);
    }

    public function test_un_code_inconnu_ne_revele_rien(): void
    {
        $this->post(route('inscription.rechercher'), ['code' => 'FLM-ZZZZZZ'])
            ->assertRedirect()
            ->assertSessionHasErrors('code');
    }

    public function test_le_recu_n_est_pas_telechargeable_tant_que_l_inscription_n_est_pas_payee(): void
    {
        $inscription = Inscription::create([
            'edition_id' => $this->edition->id,
            'participant_id' => \App\Models\Participant::create([
                'nom' => 'Traoré',
                'prenom' => 'Salif',
                'email' => 'salif@example.test',
                'telephone' => '22670000002',
            ])->id,
            'region_id' => $this->region->id,
            'profil_id' => $this->profil->id,
            'montant' => 10000,
            'statut' => 'pending',
        ]);

        $this->get(route('recu.telecharger', $inscription->code_inscription))->assertNotFound();
    }
}
