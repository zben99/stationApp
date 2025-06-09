<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Changez en false si vous voulez restreindre l'accès
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // règle d'unicité composite name+location
                Rule::unique('stations')
                    ->where(function ($query) {
                        // on compare la colonne 'location' à la valeur soumise
                        $query->where('location', $this->input('location'));
                    }),
            ],
            'location' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom de la station est obligatoire.',
            'name.unique'   => 'Une station avec ce nom et cet emplacement existe déjà.',
            'name.max'      => 'Le nom ne peut pas dépasser 255 caractères.',
            'location.max'  => 'L’emplacement ne peut pas dépasser 255 caractères.',
            'is_active.required' => 'Le statut actif est obligatoire.',
            'is_active.boolean'  => 'Le statut actif doit être vrai ou faux.',
        ];
    }
}
