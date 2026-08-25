<?php

namespace Tests\Feature\Api;

use App\Models\Appuntamento;
use App\Models\Medico;
use App\Models\Specialita;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppuntamentoTest extends TestCase
{
    use RefreshDatabase;

    private User $paziente;

    private Medico $medico;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paziente = User::factory()->create();
        $this->medico = Medico::factory()->create([
            'specialita_id' => Specialita::factory()->create(['durata_visita_minuti' => 60]),
            'ora_inizio' => '09:00:00',
            'ora_fine' => '12:00:00',
            'giorni_lavorativi' => [1, 2, 3, 4, 5],
        ]);
    }

    private function slot(int $ora = 9): CarbonImmutable
    {
        return CarbonImmutable::now()->addWeek()->startOfWeek()->setTime($ora, 0);
    }

    public function test_un_paziente_puo_prenotare_una_visita(): void
    {
        $this->actingAs($this->paziente, 'sanctum')
            ->postJson('/api/appuntamenti', [
                'medico_id' => $this->medico->id,
                'data_ora' => $this->slot()->format('Y-m-d H:i'),
                'motivo' => 'Controllo di routine',
            ])
            ->assertCreated()
            ->assertJsonPath('data.stato', Appuntamento::STATO_PRENOTATO);

        $this->assertDatabaseHas('appuntamenti', [
            'paziente_id' => $this->paziente->id,
            'medico_id' => $this->medico->id,
        ]);
    }

    public function test_non_si_puo_prenotare_uno_slot_gia_occupato(): void
    {
        Appuntamento::factory()->create([
            'medico_id' => $this->medico->id,
            'paziente_id' => User::factory(),
            'data_ora' => $this->slot(),
        ]);

        $this->actingAs($this->paziente, 'sanctum')
            ->postJson('/api/appuntamenti', [
                'medico_id' => $this->medico->id,
                'data_ora' => $this->slot()->format('Y-m-d H:i'),
            ])
            ->assertStatus(409);
    }

    public function test_non_si_puo_prenotare_fuori_dagli_orari_di_ricevimento(): void
    {
        $this->actingAs($this->paziente, 'sanctum')
            ->postJson('/api/appuntamenti', [
                'medico_id' => $this->medico->id,
                'data_ora' => $this->slot(20)->format('Y-m-d H:i'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('data_ora');
    }

    public function test_non_si_puo_prenotare_nel_passato(): void
    {
        $this->actingAs($this->paziente, 'sanctum')
            ->postJson('/api/appuntamenti', [
                'medico_id' => $this->medico->id,
                'data_ora' => CarbonImmutable::now()->subDay()->format('Y-m-d H:i'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('data_ora');
    }

    public function test_un_medico_non_puo_prenotare_visite(): void
    {
        $this->actingAs($this->medico->user, 'sanctum')
            ->postJson('/api/appuntamenti', [
                'medico_id' => $this->medico->id,
                'data_ora' => $this->slot()->format('Y-m-d H:i'),
            ])
            ->assertForbidden();
    }

    public function test_il_paziente_vede_solo_i_propri_appuntamenti(): void
    {
        Appuntamento::factory()->create([
            'paziente_id' => $this->paziente->id,
            'medico_id' => $this->medico->id,
            'data_ora' => $this->slot(9),
        ]);
        Appuntamento::factory()->create([
            'paziente_id' => User::factory(),
            'medico_id' => $this->medico->id,
            'data_ora' => $this->slot(10),
        ]);

        $this->actingAs($this->paziente, 'sanctum')
            ->getJson('/api/appuntamenti')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_un_paziente_non_puo_consultare_l_appuntamento_altrui(): void
    {
        $altrui = Appuntamento::factory()->create([
            'paziente_id' => User::factory(),
            'medico_id' => $this->medico->id,
            'data_ora' => $this->slot(),
        ]);

        $this->actingAs($this->paziente, 'sanctum')
            ->getJson('/api/appuntamenti/'.$altrui->id)
            ->assertForbidden();
    }

    public function test_il_paziente_puo_annullare_la_propria_prenotazione(): void
    {
        $appuntamento = Appuntamento::factory()->create([
            'paziente_id' => $this->paziente->id,
            'medico_id' => $this->medico->id,
            'data_ora' => $this->slot(),
        ]);

        $this->actingAs($this->paziente, 'sanctum')
            ->deleteJson('/api/appuntamenti/'.$appuntamento->id)
            ->assertOk();

        $this->assertDatabaseHas('appuntamenti', [
            'id' => $appuntamento->id,
            'stato' => Appuntamento::STATO_ANNULLATO,
        ]);
    }

    public function test_una_visita_gia_svolta_non_e_annullabile(): void
    {
        $appuntamento = Appuntamento::factory()->completato()->create([
            'paziente_id' => $this->paziente->id,
            'medico_id' => $this->medico->id,
        ]);

        $this->actingAs($this->paziente, 'sanctum')
            ->deleteJson('/api/appuntamenti/'.$appuntamento->id)
            ->assertStatus(422);
    }
}
