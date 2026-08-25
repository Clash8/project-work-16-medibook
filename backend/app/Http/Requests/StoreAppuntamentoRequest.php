<?php

namespace App\Http\Requests;

use App\Models\Medico;
use App\Services\DisponibilitaService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Centralizza la validazione della prenotazione: formato dei dati, coerenza
 * temporale e appartenenza dello slot alla griglia oraria del medico.
 */
class StoreAppuntamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPaziente() ?? false;
    }

    public function rules(): array
    {
        return [
            'medico_id' => ['required', 'integer', 'exists:medici,id'],
            'data_ora' => ['required', 'date_format:Y-m-d H:i', 'after:now'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $medico = Medico::with('specialita')->find($this->input('medico_id'));
            $dataOra = CarbonImmutable::createFromFormat('Y-m-d H:i', $this->input('data_ora'));

            if (! app(DisponibilitaService::class)->slotValido($medico, $dataOra)) {
                $validator->errors()->add(
                    'data_ora',
                    'Lo slot richiesto non rientra negli orari di ricevimento del medico.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'data_ora.after' => 'Non è possibile prenotare una visita nel passato.',
            'data_ora.date_format' => 'Il formato atteso per la data è "AAAA-MM-GG HH:MM".',
        ];
    }
}
