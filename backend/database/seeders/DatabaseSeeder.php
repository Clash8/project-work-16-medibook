<?php

namespace Database\Seeders;

use App\Models\Appuntamento;
use App\Models\Medico;
use App\Models\Referto;
use App\Models\Specialita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Popola la base dati con uno scenario dimostrativo della clinica:
 * specialità, medici con relative agende, pazienti, prenotazioni e referti.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SpecialitaSeeder::class,
            MediciSeeder::class,
        ]);

        User::factory()->admin()->create([
            'nome' => 'Laura',
            'cognome' => 'Direzione',
            'email' => 'admin@medibook.test',
            'password' => Hash::make('password123'),
        ]);

        $paziente = User::factory()->create([
            'nome' => 'Marco',
            'cognome' => 'Rossi',
            'email' => 'paziente@medibook.test',
            'password' => Hash::make('password123'),
            'codice_fiscale' => 'RSSMRC90A01F205X',
        ]);

        User::factory()->count(9)->create();

        $cardiologo = Medico::whereHas('specialita', fn ($q) => $q->where('nome', 'Cardiologia'))->first();

        // Visita già svolta, con referto disponibile per il paziente dimostrativo.
        $svolta = Appuntamento::create([
            'paziente_id' => $paziente->id,
            'medico_id' => $cardiologo->id,
            'data_ora' => now()->subDays(14)->setTime(10, 0),
            'stato' => Appuntamento::STATO_COMPLETATO,
            'motivo' => 'Controllo pressione arteriosa',
        ]);

        Referto::create([
            'appuntamento_id' => $svolta->id,
            'diagnosi' => 'Ipertensione arteriosa lieve',
            'descrizione' => 'Pressione rilevata 145/90 mmHg. Esame obiettivo cardiaco nella norma, '
                .'toni validi e ritmici, non soffi. ECG a riposo privo di alterazioni significative.',
            'prescrizione' => 'Riduzione del sodio nella dieta, attività fisica aerobica tre volte '
                .'a settimana, monitoraggio domiciliare della pressione. Controllo tra sei mesi.',
            'data_emissione' => now()->subDays(14)->toDateString(),
        ]);

        // Visita futura, annullabile dall'interfaccia del paziente.
        Appuntamento::create([
            'paziente_id' => $paziente->id,
            'medico_id' => $cardiologo->id,
            'data_ora' => now()->addDays(7)->startOfDay()->setTime(11, 0),
            'stato' => Appuntamento::STATO_PRENOTATO,
            'motivo' => 'Visita di controllo semestrale',
        ]);
    }
}
