<?php

namespace App\Services;

use App\Models\Appuntamento;
use App\Models\Medico;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Servizio di dominio che calcola gli slot prenotabili di un medico in una data.
 *
 * La logica e stata isolata dai controller per rispettare il principio di singola
 * responsabilità e per poterla riutilizzare sia in fase di consultazione delle
 * disponibilita sia in fase di validazione della prenotazione.
 */
class DisponibilitaService
{
    /**
     * Restituisce gli orari liberi del medico nella data indicata, in formato "H:i".
     *
     * @return Collection<int, string>
     */
    public function slotLiberi(Medico $medico, CarbonImmutable $data): Collection
    {
        if (! $medico->lavoraIlGiorno($data->dayOfWeekIso)) {
            return collect();
        }

        $occupati = $this->slotOccupati($medico, $data);

        return $this->slotTeorici($medico, $data)
            ->reject(fn (CarbonImmutable $slot) => $slot->isPast())
            ->reject(fn (CarbonImmutable $slot) => $occupati->contains($slot->format('Y-m-d H:i:s')))
            ->map(fn (CarbonImmutable $slot) => $slot->format('H:i'))
            ->values();
    }

    /** Vero se lo slot richiesto rientra nella griglia oraria del medico. */
    public function slotValido(Medico $medico, CarbonImmutable $dataOra): bool
    {
        if (! $medico->lavoraIlGiorno($dataOra->dayOfWeekIso)) {
            return false;
        }

        return $this->slotTeorici($medico, $dataOra)
            ->contains(fn (CarbonImmutable $slot) => $slot->equalTo($dataOra));
    }

    /**
     * Griglia completa degli slot della giornata, indipendentemente dalle prenotazioni.
     *
     * @return Collection<int, CarbonImmutable>
     */
    private function slotTeorici(Medico $medico, CarbonImmutable $data): Collection
    {
        $passo = max(5, $medico->specialita->durata_visita_minuti);
        $cursore = $this->componiOrario($data, $medico->ora_inizio);
        $fine = $this->componiOrario($data, $medico->ora_fine);

        $slot = collect();

        while ($cursore->addMinutes($passo)->lessThanOrEqualTo($fine)) {
            $slot->push($cursore);
            $cursore = $cursore->addMinutes($passo);
        }

        return $slot;
    }

    /**
     * Orari già impegnati da appuntamenti non annullati.
     *
     * @return Collection<int, string>
     */
    private function slotOccupati(Medico $medico, CarbonImmutable $data): Collection
    {
        return Appuntamento::query()
            ->where('medico_id', $medico->id)
            ->where('stato', '!=', Appuntamento::STATO_ANNULLATO)
            ->whereDate('data_ora', $data->toDateString())
            ->pluck('data_ora')
            ->map(fn ($dataOra) => CarbonImmutable::parse($dataOra)->format('Y-m-d H:i:s'));
    }

    private function componiOrario(CarbonImmutable $data, string $orario): CarbonImmutable
    {
        [$ore, $minuti] = array_pad(explode(':', $orario), 2, '0');

        return $data->setTime((int) $ore, (int) $minuti);
    }
}
