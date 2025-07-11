<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Autoriser tous les utilisateurs (changer si nécessaire)
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unicité composite: name + location, en ignorant l'enregistrement courant
                Rule::unique('stations')
                    ->where(function ($query) {
                        $query->where('location', $this->input('location'));
                    })
                    ->ignore($this->station->id),
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom de la station est obligatoire.',
            'name.unique' => 'Une station avec ce nom et cet emplacement existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'location.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            'is_active.required' => 'Le statut actif est obligatoire.',
            'is_active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }
}
