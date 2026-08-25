<?php

namespace Database\Factories;

use App\Models\Appuntamento;
use App\Models\Referto;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Referto> */
class RefertoFactory extends Factory
{
    protected $model = Referto::class;

    public function definition(): array
    {
        return [
            'appuntamento_id' => Appuntamento::factory()->completato(),
            'diagnosi' => fake('it_IT')->sentence(3),
            'descrizione' => fake('it_IT')->paragraph(),
            'prescrizione' => fake('it_IT')->sentence(6),
            'data_emissione' => now()->toDateString(),
        ];
    }
}
