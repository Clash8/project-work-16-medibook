<?php

namespace Tests\Feature\Api;

use App\Models\Appuntamento;
use App\Models\Medico;
use App\Models\Referto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefertoTest extends TestCase
{
    use RefreshDatabase;

    public function test_il_medico_puo_emettere_il_referto_della_propria_visita(): void
    {
        $medico = Medico::factory()->create();
        $appuntamento = Appuntamento::factory()->completato()->create(['medico_id' => $medico->id]);

        $this->actingAs($medico->user, 'sanctum')
            ->postJson('/api/referti', [
                'appuntamento_id' => $appuntamento->id,
                'diagnosi' => 'Ipertensione lieve',
                'descrizione' => 'Esame obiettivo nella norma, pressione 145/90 mmHg.',
                'prescrizione' => 'Controllo tra sei mesi.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.diagnosi', 'Ipertensione lieve');

        $this->assertDatabaseHas('appuntamenti', [
            'id' => $appuntamento->id,
            'stato' => Appuntamento::STATO_COMPLETATO,
        ]);
    }

    public function test_un_medico_non_puo_refertare_la_visita_di_un_collega(): void
    {
        $collega = Medico::factory()->create();
        $medico = Medico::factory()->create();
        $appuntamento = Appuntamento::factory()->completato()->create(['medico_id' => $collega->id]);

        $this->actingAs($medico->user, 'sanctum')
            ->postJson('/api/referti', [
                'appuntamento_id' => $appuntamento->id,
                'diagnosi' => 'Diagnosi',
                'descrizione' => 'Descrizione',
            ])
            ->assertForbidden();
    }

    public function test_un_paziente_non_puo_emettere_referti(): void
    {
        $paziente = User::factory()->create();
        $appuntamento = Appuntamento::factory()->completato()->create(['paziente_id' => $paziente->id]);

        $this->actingAs($paziente, 'sanctum')
            ->postJson('/api/referti', [
                'appuntamento_id' => $appuntamento->id,
                'diagnosi' => 'Diagnosi',
                'descrizione' => 'Descrizione',
            ])
            ->assertForbidden();
    }

    public function test_il_paziente_consulta_solo_i_propri_referti(): void
    {
        $paziente = User::factory()->create();

        Referto::factory()->create([
            'appuntamento_id' => Appuntamento::factory()->completato()->create(['paziente_id' => $paziente->id]),
        ]);
        Referto::factory()->create();

        $this->actingAs($paziente, 'sanctum')
            ->getJson('/api/referti')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_il_referto_altrui_non_e_consultabile(): void
    {
        $paziente = User::factory()->create();
        $referto = Referto::factory()->create();

        $this->actingAs($paziente, 'sanctum')
            ->getJson('/api/referti/'.$referto->id)
            ->assertForbidden();
    }
}
