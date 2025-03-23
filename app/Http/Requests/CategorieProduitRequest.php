<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategorieProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // à adapter en fonction de la logique d'autorisation
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:fuel,stock'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'type.required' => 'Le type est obligatoire.',
            'type.in' => 'Le type doit être soit fuel ou stock.',
            'is_active.boolean' => 'Le champ actif doit être vrai ou faux.',
        ];
    }
}

