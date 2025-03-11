<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Station;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            'user_id' => ['required', 'exists:users,id'],
            'station_id' => ['required', 'exists:stations,id'],
        ]);

        $user = User::findOrFail($request->user_id);
        $user->station_id = $request->station_id;
        $user->save();

        return redirect()->route('stations.associate')->with('success', 'Utilisateur associé à la station avec succès.');
    }

    public function detach(User $user)
    {
        $user->station_id = null;
        $user->save();

        return redirect()->route('stations.associate')->with('success', 'Utilisateur dissocié de la station.');
    }
}

