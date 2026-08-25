<?php

namespace Tests\Feature\Api;

use App\Models\Appuntamento;
use App\Models\Medico;
use App\Models\Specialita;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisponibilitaTest extends TestCase
{
    use RefreshDatabase;

    private function prossimoLunedi(): CarbonImmutable
    {
        return CarbonImmutable::now()->addWeek()->startOfWeek();
    }

    public function test_gli_slot_sono_generati_secondo_la_durata_della_visita(): void
    {
        $medico = Medico::factory()->create([
            'specialita_id' => Specialita::factory()->create(['durata_visita_minuti' => 60]),
            'ora_inizio' => '09:00:00',
            'ora_fine' => '12:00:00',
            'giorni_lavorativi' => [1, 2, 3, 4, 5],
        ]);

        $risposta = $this->getJson(sprintf(
            '/api/disponibilita?medico=%d&data=%s',
            $medico->id,
            $this->prossimoLunedi()->toDateString()
        ));

        $risposta->assertOk()->assertJsonPath('data.slot', ['09:00', '10:00', '11:00']);
    }

    public function test_uno_slot_gia_prenotato_non_viene_proposto(): void
    {
        $medico = Medico::factory()->create([
            'specialita_id' => Specialita::factory()->create(['durata_visita_minuti' => 60]),
            'ora_inizio' => '09:00:00',
            'ora_fine' => '12:00:00',
            'giorni_lavorativi' => [1, 2, 3, 4, 5],
        ]);

        $lunedi = $this->prossimoLunedi();

        Appuntamento::factory()->create([
            'medico_id' => $medico->id,
            'paziente_id' => User::factory(),
            'data_ora' => $lunedi->setTime(10, 0),
        ]);

        $this->getJson(sprintf('/api/disponibilita?medico=%d&data=%s', $medico->id, $lunedi->toDateString()))
            ->assertOk()
            ->assertJsonPath('data.slot', ['09:00', '11:00']);
    }

    public function test_nei_giorni_non_lavorativi_non_vi_sono_slot(): void
    {
        $medico = Medico::factory()->create(['giorni_lavorativi' => [1, 2, 3, 4, 5]]);
        $domenica = $this->prossimoLunedi()->addDays(6);

        $this->getJson(sprintf('/api/disponibilita?medico=%d&data=%s', $medico->id, $domenica->toDateString()))
            ->assertOk()
            ->assertJsonPath('data.slot', []);
    }

    public function test_la_data_deve_essere_nel_formato_atteso(): void
    {
        $medico = Medico::factory()->create();

        $this->getJson('/api/disponibilita?medico='.$medico->id.'&data=01-09-2026')
            ->assertStatus(422)
            ->assertJsonValidationErrors('data');
    }
}
