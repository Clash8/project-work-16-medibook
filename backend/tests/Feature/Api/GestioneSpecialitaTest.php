<?php

namespace Tests\Feature\Api;

use App\Models\Medico;
use App\Models\Specialita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GestioneSpecialitaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    private function datiSpecialita(array $sovrascritture = []): array
    {
        return [
            'nome' => 'Ortopedia',
            'descrizione' => 'Diagnosi e cura delle patologie dell\'apparato muscolo-scheletrico.',
            'durata_visita_minuti' => 30,
            'costo' => 110,
            ...$sovrascritture,
        ];
    }

    public function test_l_amministratore_puo_creare_una_specialita(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/specialita', $this->datiSpecialita())
            ->assertCreated()
            ->assertJsonPath('data.nome', 'Ortopedia');

        $this->assertDatabaseHas('specialita', ['nome' => 'Ortopedia']);
    }

    public function test_il_nome_della_specialita_deve_essere_univoco(): void
    {
        Specialita::factory()->create(['nome' => 'Ortopedia']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/specialita', $this->datiSpecialita())
            ->assertStatus(422)
            ->assertJsonValidationErrors('nome');
    }

    public function test_l_amministratore_puo_modificare_una_specialita(): void
    {
        $specialita = Specialita::factory()->create(['nome' => 'Ortopedia']);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/specialita/'.$specialita->id, $this->datiSpecialita(['costo' => 95]))
            ->assertOk()
            ->assertJsonPath('data.costo', 95);
    }

    public function test_una_specialita_con_medici_associati_non_e_eliminabile(): void
    {
        $specialita = Specialita::factory()->create();
        Medico::factory()->create(['specialita_id' => $specialita->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/specialita/'.$specialita->id)
            ->assertStatus(409);

        $this->assertDatabaseHas('specialita', ['id' => $specialita->id]);
    }

    public function test_l_amministratore_puo_eliminare_una_specialita_senza_medici(): void
    {
        $specialita = Specialita::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/specialita/'.$specialita->id)
            ->assertOk();

        $this->assertDatabaseMissing('specialita', ['id' => $specialita->id]);
    }

    public function test_solo_l_amministratore_puo_gestire_le_specialita(): void
    {
        $this->postJson('/api/specialita', $this->datiSpecialita())
            ->assertUnauthorized();

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('/api/specialita', $this->datiSpecialita())
            ->assertForbidden();
    }
}
