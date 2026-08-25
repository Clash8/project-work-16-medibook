<?php

namespace Database\Factories;

use App\Models\Specialita;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Specialita> */
class SpecialitaFactory extends Factory
{
    protected $model = Specialita::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->word(),
            'descrizione' => fake('it_IT')->sentence(),
            'durata_visita_minuti' => 30,
            'costo' => fake()->randomFloat(2, 40, 200),
        ];
    }
}
