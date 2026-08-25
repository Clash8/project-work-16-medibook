<?php

namespace Database\Seeders;

use App\Models\Medico;
use App\Models\Specialita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MediciSeeder extends Seeder
{
    public function run(): void
    {
        $medici = [
            ['nome' => 'Giulia', 'cognome' => 'Bianchi', 'specialita' => 'Cardiologia', 'albo' => 'MI-100234', 'inizio' => '09:00:00', 'fine' => '17:00:00', 'giorni' => [1, 2, 3, 4, 5]],
            ['nome' => 'Andrea', 'cognome' => 'Conti', 'specialita' => 'Cardiologia', 'albo' => 'MI-100987', 'inizio' => '14:00:00', 'fine' => '19:00:00', 'giorni' => [1, 3, 5]],
            ['nome' => 'Sara', 'cognome' => 'Ferrari', 'specialita' => 'Dermatologia', 'albo' => 'MI-101455', 'inizio' => '08:30:00', 'fine' => '13:30:00', 'giorni' => [2, 4]],
            ['nome' => 'Paolo', 'cognome' => 'Greco', 'specialita' => 'Ortopedia', 'albo' => 'MI-102876', 'inizio' => '09:00:00', 'fine' => '18:00:00', 'giorni' => [1, 2, 4, 5]],
            ['nome' => 'Elena', 'cognome' => 'Marchetti', 'specialita' => 'Ginecologia', 'albo' => 'MI-103012', 'inizio' => '10:00:00', 'fine' => '16:00:00', 'giorni' => [1, 3, 4]],
            ['nome' => 'Davide', 'cognome' => 'Russo', 'specialita' => 'Oculistica', 'albo' => 'MI-103771', 'inizio' => '09:00:00', 'fine' => '15:00:00', 'giorni' => [2, 3, 5]],
            ['nome' => 'Chiara', 'cognome' => 'Villa', 'specialita' => 'Medicina generale', 'albo' => 'MI-104590', 'inizio' => '08:00:00', 'fine' => '14:00:00', 'giorni' => [1, 2, 3, 4, 5]],
        ];

        foreach ($medici as $indice => $dati) {
            $utente = User::updateOrCreate(
                ['email' => sprintf('%s.%s@medibook.test', strtolower($dati['nome']), strtolower($dati['cognome']))],
                [
                    'nome' => $dati['nome'],
                    'cognome' => $dati['cognome'],
                    'password' => Hash::make('password123'),
                    'ruolo' => User::RUOLO_MEDICO,
                    'telefono' => '02'.str_pad((string) (1000000 + $indice), 8, '0', STR_PAD_LEFT),
                ]
            );

            Medico::updateOrCreate(
                ['user_id' => $utente->id],
                [
                    'specialita_id' => Specialita::where('nome', $dati['specialita'])->value('id'),
                    'numero_albo' => $dati['albo'],
                    'biografia' => sprintf(
                        'Specialista in %s, esercita presso la clinica con particolare attenzione alla prevenzione e al follow-up dei pazienti cronici.',
                        strtolower($dati['specialita'])
                    ),
                    'ora_inizio' => $dati['inizio'],
                    'ora_fine' => $dati['fine'],
                    'giorni_lavorativi' => $dati['giorni'],
                ]
            );
        }
    }
}
