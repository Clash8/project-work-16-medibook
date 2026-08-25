<?php

namespace Database\Seeders;

use App\Models\Specialita;
use Illuminate\Database\Seeder;

class SpecialitaSeeder extends Seeder
{
    public function run(): void
    {
        $specialita = [
            ['nome' => 'Cardiologia', 'descrizione' => 'Diagnosi e cura delle patologie del cuore e del sistema circolatorio.', 'durata_visita_minuti' => 30, 'costo' => 120.00],
            ['nome' => 'Dermatologia', 'descrizione' => 'Prevenzione e trattamento delle patologie della pelle e degli annessi cutanei.', 'durata_visita_minuti' => 20, 'costo' => 90.00],
            ['nome' => 'Ortopedia', 'descrizione' => 'Cura dell apparato muscolo-scheletrico, traumi e patologie articolari.', 'durata_visita_minuti' => 30, 'costo' => 110.00],
            ['nome' => 'Ginecologia', 'descrizione' => 'Salute femminile, prevenzione oncologica e monitoraggio della gravidanza.', 'durata_visita_minuti' => 30, 'costo' => 100.00],
            ['nome' => 'Oculistica', 'descrizione' => 'Valutazione della vista e cura delle patologie oculari.', 'durata_visita_minuti' => 20, 'costo' => 80.00],
            ['nome' => 'Medicina generale', 'descrizione' => 'Visite generiche, certificazioni e primo inquadramento diagnostico.', 'durata_visita_minuti' => 15, 'costo' => 50.00],
        ];

        foreach ($specialita as $dati) {
            Specialita::updateOrCreate(['nome' => $dati['nome']], $dati);
        }
    }
}
