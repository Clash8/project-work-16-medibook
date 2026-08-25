<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descrizione' => $this->descrizione,
            'durata_visita_minuti' => $this->durata_visita_minuti,
            'costo' => (float) $this->costo,
        ];
    }
}
