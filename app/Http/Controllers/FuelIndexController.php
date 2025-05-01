<?php
namespace App\Http\Controllers;

use App\Models\FuelIndex;
use App\Models\Pump;
use Illuminate\Http\Request;

class FuelIndexController extends Controller
{
    public function index()
    {
        //dd('');
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
        $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'pumps' => 'required|array|min:1',
            'pumps.*.pump_id' => 'required|exists:pumps,id',
            'pumps.*.index_debut' => 'required|numeric|min:0',
            'pumps.*.index_fin' => 'required|numeric|gte:pumps.*.index_debut',
            'pumps.*.montant_declare' => 'nullable|numeric|min:0',
            'pumps.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $stationId = session('selected_station_id');
        $userId = auth()->id();

        // 🔒 Vérifie si une saisie existe déjà pour la date et la rotation
        $existing = \App\Models\FuelIndex::where('station_id', $stationId)
            ->whereDate('date', $request->date)
            ->where('rotation', $request->rotation)
            ->exists();

        if ($existing) {
            return back()->withErrors([
                'rotation' => 'Un relevé pour cette date et cette rotation existe déjà.'
            ])->withInput();
        }

        // ✅ Enregistrement des données
        foreach ($request->pumps as $pumpData) {
            if ($pumpData['index_fin'] < $pumpData['index_debut']) {
                continue;
            }

            FuelIndex::create([
                'station_id'      => $stationId,
                'pump_id'         => $pumpData['pump_id'],
                'user_id'         => $userId,
                'date'            => $request->date,
                'rotation'        => $request->rotation,
                'index_debut'     => $pumpData['index_debut'],
                'index_fin'       => $pumpData['index_fin'],
                'prix_unitaire'   => $pumpData['prix_unitaire'],
                'montant_declare' => $pumpData['montant_declare'] ?? null,
            ]);
        }

        return redirect()->route('fuel-indexes.index')->with('success', 'Relevés journaliers enregistrés avec succès.');
    }




    public function details($date, $rotation)
{
    $stationId = session('selected_station_id');

    $entries = FuelIndex::with('pump.tank.product', 'user')
        ->where('station_id', $stationId)
        ->whereDate('date', $date)
        ->where('rotation', $rotation)
        ->get();

    return view('fuel_indexes.details', compact('entries', 'date', 'rotation'));
}


}
