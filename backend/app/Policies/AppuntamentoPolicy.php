<?php

namespace App\Policies;

use App\Models\Appuntamento;
use App\Models\User;

/**
 * Regole di autorizzazione sugli appuntamenti: il paziente accede solo alle
 * proprie prenotazioni, il medico solo a quelle della propria agenda.
 */
class AppuntamentoPolicy
{
    public function view(User $utente, Appuntamento $appuntamento): bool
    {
        if ($utente->isAdmin()) {
            return true;
        }

        if ($utente->isMedico()) {
            return $appuntamento->medico_id === $utente->medico?->id;
        }

        return $appuntamento->paziente_id === $utente->id;
    }

    public function delete(User $utente, Appuntamento $appuntamento): bool
    {
        return $utente->isAdmin() || $appuntamento->paziente_id === $utente->id;
    }
}
