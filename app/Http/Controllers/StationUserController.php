<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;

class StationUserController extends Controller
{
    public function index()
    {
        $stations = Station::orderBy('name')->get();

        // Exclure les super utilisateurs et charger les relations stations
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Super Gestionnaire', 'super-admin']);
        })
            ->with('stations')
            ->orderBy('name')
            ->get();

        return view('stations.associate', compact('stations', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'station_id' => 'required|array',
            'station_id.*' => 'exists:stations,id',
        ], [
            'user_id.required' => 'Veuillez sélectionner un utilisateur.',
            'station_id.required' => 'Veuillez sélectionner au moins une station.',
            'station_id.*.exists' => 'Une station sélectionnée est invalide.',
        ]);

        $user = User::findOrFail($request->user_id);

        // Associer sans détacher les stations déjà associées
        $user->stations()->syncWithoutDetaching($request->station_id);

        return redirect()->route('stations.associate')->with('success', 'Utilisateur associé aux stations avec succès.');
    }

    public function detach(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'station_id' => 'required|exists:stations,id',
        ], [
            'user_id.required' => 'Utilisateur introuvable.',
            'station_id.required' => 'Station invalide.',
        ]);

        $user = User::findOrFail($request->user_id);

        $user->stations()->detach($request->station_id);

        return redirect()->route('stations.associate')->with('success', 'Utilisateur dissocié de la station.');
    }
}
