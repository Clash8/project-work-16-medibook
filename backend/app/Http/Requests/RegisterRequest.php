<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'cognome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'telefono' => ['nullable', 'string', 'max:20'],
            'codice_fiscale' => ['nullable', 'string', 'size:16', 'unique:users,codice_fiscale'],
            'data_nascita' => ['nullable', 'date', 'before:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Esiste già un account registrato con questa email.',
            'password.confirmed' => 'Le due password non coincidono.',
        ];
    }
}
