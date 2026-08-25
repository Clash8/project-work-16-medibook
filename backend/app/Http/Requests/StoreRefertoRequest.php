<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefertoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMedico() ?? false;
    }

    public function rules(): array
    {
        return [
            'appuntamento_id' => ['required', 'integer', 'exists:appuntamenti,id', 'unique:referti,appuntamento_id'],
            'diagnosi' => ['required', 'string', 'max:255'],
            'descrizione' => ['required', 'string'],
            'prescrizione' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'appuntamento_id.unique' => 'Per questa visita è già stato emesso un referto.',
        ];
    }
}
