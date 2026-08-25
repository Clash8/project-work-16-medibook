<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome_completo' => $this->nome_completo,
            'numero_albo' => $this->numero_albo,
            'biografia' => $this->biografia,
            'ora_inizio' => substr((string) $this->ora_inizio, 0, 5),
            'ora_fine' => substr((string) $this->ora_fine, 0, 5),
            'giorni_lavorativi' => $this->giorni_lavorativi,
            'specialita' => new SpecialitaResource($this->whenLoaded('specialita')),
        ];
    }
}
