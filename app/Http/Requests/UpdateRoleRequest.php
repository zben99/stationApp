<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette demande.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Si tu veux restreindre l'accès à certains utilisateurs, tu peux ajouter une logique ici
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la demande.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255', // Validation pour le nom du rôle
            'permission' => 'required|array', // Les permissions doivent être un tableau
            'permission.*' => 'exists:permissions,id', // Chaque permission doit exister dans la table des permissions
        ];
    }

    /**
     * Personnaliser les messages de validation (facultatif).
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Le nom du rôle est obligatoire.',
            'permission.required' => 'Vous devez sélectionner au moins une permission.',
            'permission.*.exists' => 'Une ou plusieurs permissions sélectionnées sont invalides.',
        ];
    }
}
