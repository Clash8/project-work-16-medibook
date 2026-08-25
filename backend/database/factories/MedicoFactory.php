<?php

namespace Database\Factories;

use App\Models\Medico;
use App\Models\Specialita;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Medico> */
class MedicoFactory extends Factory
{
    protected $model = Medico::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->medico(),
            'specialita_id' => Specialita::factory(),
            'numero_albo' => fake()->unique()->numerify('MI-######'),
            'biografia' => fake('it_IT')->paragraph(),
            'ora_inizio' => '09:00:00',
            'ora_fine' => '17:00:00',
            'giorni_lavorativi' => [1, 2, 3, 4, 5],
        ];
    }
}
