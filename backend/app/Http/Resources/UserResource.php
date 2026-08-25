<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'cognome' => $this->cognome,
            'nome_completo' => $this->nome_completo,
            'email' => $this->email,
            'ruolo' => $this->ruolo,
            'telefono' => $this->telefono,
            'codice_fiscale' => $this->codice_fiscale,
            'medico_id' => $this->whenLoaded('medico', fn () => $this->medico?->id),
        ];
    }
}
