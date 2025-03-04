<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
            'permission.*' => 'integer|exists:permissions,id',
        ];
    }


public function messages()
{
    return [
        'name.required' => 'Le nom du rôle est obligatoire.',
        'name.unique' => 'Ce nom de rôle existe déjà.',
        'permission.required' => 'Veuillez sélectionner au moins une permission.',
        'permission.array' => 'Le format des permissions est invalide.',
        'permission.*.integer' => 'Chaque permission doit être un identifiant valide.',
        'permission.*.exists' => 'Une ou plusieurs permissions sélectionnées sont invalides.',
    ];
}

}
