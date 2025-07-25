<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StationProduct;
use App\Models\Tank;
use App\Models\TankStock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TankController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');

        $tanks = Tank::with(['station', 'product', 'stock'])
            ->where('station_id', $stationId)
            ->orderBy('code')
            ->get();

        return view('tanks.index', compact('tanks'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');

        $products = StationProduct::where('station_id', $stationId)
            ->whereHas('stationCategory', function ($q) {
                $q->where('type', 'fuel');
            })
            ->get();

        return view('tanks.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tanks')->where(function ($query) {
                    return $query->where('station_id', session('selected_station_id'));
                }),
            ],
            'capacite' => 'required|numeric|min:0',
        ]);


        // Injecter la station depuis la session
        $data['station_id'] = session('selected_station_id');

        $tank = Tank::create($data);

        TankStock::create([
            'tank_id' => $tank->id,
            'quantite_actuelle' => 0,
        ]);

        return redirect()->route('tanks.index')->with('success', 'Cuve créée avec succès.');
    }

    public function edit(Tank $tank)
    {
        $stations = Station::all();
        $products = StationProduct::whereHas('stationCategory', function ($q) {
            $q->where('type', 'fuel');
        })->get();

        return view('tanks.edit', compact('tank', 'stations', 'products'));
    }

    public function update(Request $request, Tank $tank)
    {
        $data = $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tanks')->where(function ($query) {
                    return $query->where('station_id', session('selected_station_id'));
                })->ignore($tank->id),
            ],
            'capacite' => 'required|numeric|min:0',
        ]);

        // Injecter la station depuis la session
        $data['station_id'] = session('selected_station_id');

        $tank->update($data);

        return redirect()->route('tanks.index')->with('success', 'Cuve modifiée avec succès.');
    }

    public function destroy(Tank $tank)
    {
        $tank->delete();

        return redirect()->route('tanks.index')->with('success', 'Cuve supprimée avec succès.');
    }
}
