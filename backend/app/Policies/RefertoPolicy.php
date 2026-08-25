<?php

namespace App\Policies;

use App\Models\Appuntamento;
use App\Models\Referto;
use App\Models\User;

class RefertoPolicy
{
    public function view(User $utente, Referto $referto): bool
    {
        if ($utente->isAdmin()) {
            return true;
        }

        if ($utente->isMedico()) {
            return $referto->appuntamento->medico_id === $utente->medico?->id;
        }

        return $referto->appuntamento->paziente_id === $utente->id;
    }

    /** Solo il medico titolare della visita può emettere il referto corrispondente. */
    public function create(User $utente, Appuntamento $appuntamento): bool
    {
        return $utente->isMedico() && $appuntamento->medico_id === $utente->medico?->id;
    }
}
