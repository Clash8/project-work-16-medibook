<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nome' => fake('it_IT')->firstName(),
            'cognome' => fake('it_IT')->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'ruolo' => User::RUOLO_PAZIENTE,
            'telefono' => fake('it_IT')->numerify('3#########'),
            'codice_fiscale' => Str::upper(Str::random(16)),
            'data_nascita' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'remember_token' => Str::random(10),
        ];
    }

    public function medico(): static
    {
        return $this->state(fn () => ['ruolo' => User::RUOLO_MEDICO]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['ruolo' => User::RUOLO_ADMIN]);
    }
}
