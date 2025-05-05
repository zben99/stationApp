<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index(Request $request)
    {

        $data = Station::latest()->paginate(5);

        return view('stations.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        return view('stations.create');
    }

    public function store(StoreStationRequest $request)
    {
        Station::create($request->validated());

        return redirect()->route('stations.index')->with('success', 'Station ajoutée avec succès.');
    }

    public function edit(Station $station)
    {
        return view('stations.edit', compact('station'));
    }

    public function update(UpdateStationRequest $request, Station $station)
    {
        $station->update($request->validated());

        return redirect()->route('stations.index')->with('success', 'Station mise à jour avec succès.');
    }

    public function destroy(Station $station)
    {
        $station->delete();

        return redirect()->route('stations.index')->with('success', 'Station supprimée.');
    }
}
