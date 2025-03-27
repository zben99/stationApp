<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autoriser l'exécution pour tous les utilisateurs
    }

    public function rules(): array
    {

        $userId = $this->route('user'); // Récupère l'ID de l'utilisateur depuis la route
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $userId,
            'password' => 'nullable|confirmed',
            'roles' => 'required'
        ];
    }
}

