<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Disaccoppia il modello Eloquent dalla rappresentazione JSON esposta dalle API:
 * il contratto verso il front-end resta stabile anche se lo schema dati cambia.
 */
class AppuntamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data_ora' => $this->data_ora->format('Y-m-d H:i'),
            'stato' => $this->stato,
            'motivo' => $this->motivo,
            'note' => $this->note,
            'annullabile' => $this->isAnnullabile(),
            'medico' => $this->whenLoaded('medico', fn () => [
                'id' => $this->medico->id,
                'nome_completo' => $this->medico->nome_completo,
                'specialita' => $this->medico->specialita->nome,
            ]),
            'paziente' => $this->whenLoaded('paziente', fn () => [
                'id' => $this->paziente->id,
                'nome_completo' => $this->paziente->nome_completo,
            ]),
            'referto_id' => $this->whenLoaded('referto', fn () => $this->referto?->id),
        ];
    }
}
