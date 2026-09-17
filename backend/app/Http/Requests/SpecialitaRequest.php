<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validazione dei dati di una specialita, condivisa fra creazione e modifica. */
class SpecialitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100', Rule::unique('specialita', 'nome')->ignore($this->route('specialita'))],
            'descrizione' => ['nullable', 'string'],
            'durata_visita_minuti' => ['required', 'integer', 'min:10', 'max:240'],
            'costo' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.unique' => 'Esiste già una specialità con questo nome.',
        ];
    }
}
