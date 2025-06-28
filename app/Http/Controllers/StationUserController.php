<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;

class StationUserController extends Controller
{
    public function index()
    {
        $stations = Station::all();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super-admin']);
        })->get();

        return view('stations.associate', compact('stations', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'station_id' => 'required|array',
            'station_id.*' => 'exists:stations,id',
        ]);

        $user = User::findOrFail($request->user_id);

        foreach ($request->station_id as $stationId) {
            $user->stations()->syncWithoutDetaching([$stationId]);
        }

        return redirect()->route('stations.associate')->with('success', 'Utilisateur associé aux stations avec succès.');
    }

    public function detach(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'station_id' => 'required|exists:stations,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->stations()->detach($request->station_id);

        return redirect()->route('stations.associate')->with('success', 'Utilisateur dissocié de la station.');
    }
}
