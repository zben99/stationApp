<?php
namespace App\Http\Controllers;

use App\Models\FuelIndex;
use App\Models\Pump;
use Illuminate\Http\Request;

class FuelIndexController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $fuelIndexes = FuelIndex::with('pump', 'user')
            ->where('station_id', $stationId)
            ->orderByDesc('date')
            ->orderBy('rotation')
            ->paginate(15);

        return view('fuel_indexes.index', compact('fuelIndexes'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');
        $pumps = Pump::where('station_id', $stationId)->get();
        return view('fuel_indexes.create', compact('pumps'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pump_id' => 'required|exists:pumps,id',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'index_debut' => 'required|numeric|min:0',
            'index_fin' => 'required|numeric|gte:index_debut',
            'prix_unitaire' => 'required|numeric|min:0'
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['user_id'] = auth()->id();

        FuelIndex::create($data);

        return redirect()->route('fuel-indexes.index')->with('success', 'Vente enregistrée avec succès.');
    }
}
