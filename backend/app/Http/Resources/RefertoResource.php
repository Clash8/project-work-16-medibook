<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefertoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'diagnosi' => $this->diagnosi,
            'descrizione' => $this->descrizione,
            'prescrizione' => $this->prescrizione,
            'data_emissione' => $this->data_emissione->toDateString(),
            'appuntamento' => new AppuntamentoResource($this->whenLoaded('appuntamento')),
        ];
    }
}
