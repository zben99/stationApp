<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\Tank;
use Illuminate\Http\Request;

class PumpController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $pumps = Pump::with('tank')->where('station_id', $stationId)->get();

        return view('pumps.index', compact('pumps'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');
        $tanks = Tank::where('station_id', $stationId)->get();

        return view('pumps.create', compact('tanks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tank_id' => 'required|exists:tanks,id',
        ]);

        $data['station_id'] = session('selected_station_id');
        Pump::create($data);

        return redirect()->route('pumps.index')->with('success', 'Pompe enregistrée avec succès.');
    }

    public function edit(Pump $pump)
    {
        $stationId = session('selected_station_id');
        $tanks = Tank::where('station_id', $stationId)->get();

        return view('pumps.edit', compact('pump', 'tanks'));
    }

    public function update(Request $request, Pump $pump)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tank_id' => 'required|exists:tanks,id',
        ]);

        $pump->update($data);

        return redirect()->route('pumps.index')->with('success', 'Pompe mise à jour avec succès.');
    }

    public function destroy(Pump $pump)
    {
        $pump->delete();

        return back()->with('success', 'Pompe supprimée avec succès.');
    }
}
