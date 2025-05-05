<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Change en false si besoin de restriction d'accès
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:stations,name',
            'location' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom de la station est obligatoire.',
            'name.unique' => 'Ce nom de station existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'location.max' => 'L\'emplacement ne peut pas dépasser 255 caractères.',
            'is_active.required' => 'Le statut actif est obligatoire.',
            'is_active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }
}
