<?php

namespace Tests\Feature\Api;

use App\Models\Medico;
use App\Models\Specialita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_elenco_specialita_accessibile_pubblicamente(): void
    {
        Specialita::factory()->count(3)->create();

        $this->getJson('/api/specialita')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_i_medici_sono_filtrabili_per_specialita(): void
    {
        $cardiologia = Specialita::factory()->create(['nome' => 'Cardiologia']);
        $dermatologia = Specialita::factory()->create(['nome' => 'Dermatologia']);

        Medico::factory()->count(2)->create(['specialita_id' => $cardiologia->id]);
        Medico::factory()->create(['specialita_id' => $dermatologia->id]);

        $this->getJson('/api/medici?specialita='.$cardiologia->id)
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.specialita.nome', 'Cardiologia');
    }

    public function test_il_filtro_su_una_specialita_inesistente_e_rifiutato(): void
    {
        $this->getJson('/api/medici?specialita=999')
            ->assertStatus(422)
            ->assertJsonValidationErrors('specialita');
    }
}
