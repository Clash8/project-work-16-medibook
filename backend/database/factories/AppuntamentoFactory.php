<?php

namespace Database\Factories;

use App\Models\Appuntamento;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appuntamento> */
class AppuntamentoFactory extends Factory
{
    protected $model = Appuntamento::class;

    public function definition(): array
    {
        return [
            'paziente_id' => User::factory(),
            'medico_id' => Medico::factory(),
            'data_ora' => now()->addWeek()->setTime(10, 0),
            'stato' => Appuntamento::STATO_PRENOTATO,
            'motivo' => fake('it_IT')->sentence(4),
        ];
    }

    public function completato(): static
    {
        return $this->state(fn () => [
            'stato' => Appuntamento::STATO_COMPLETATO,
            'data_ora' => now()->subWeek()->setTime(10, 0),
        ]);
    }
}
