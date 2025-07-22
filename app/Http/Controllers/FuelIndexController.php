<?php

namespace App\Http\Controllers;

use App\Models\DailyRevenueValidation;
use App\Models\FuelIndex;
use App\Models\Pump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelIndexController extends Controller
{
    public function index()
    {
        // dd('');
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
        $rotation = request('rotation') ?? '6-14';
        $date = date('Y-m-d');

        $pumps = Pump::with('tank.product')
            ->where('station_id', $stationId)
            ->get()
            ->sortBy(function ($pump) {
                $productName = strtolower($pump->tank->product->name ?? '');

                return str_contains($productName, 'super') ? '0_'.$pump->name : '1_'.$pump->name;
            })
            ->values();

        // Liste des rotations par ordre chronologique
        $rotationOrder = ['6-14', '14-22', '22-6'];

        // Trouver l'index de la rotation courante
        $currentRotationIndex = array_search($rotation, $rotationOrder);

        // Préparer tableau des derniers index par pompe
        $lastIndexes = [];

        foreach ($pumps as $pump) {
            // Chercher la dernière saisie antérieure pour cette pompe
            $lastFuelIndex = FuelIndex::where('station_id', $stationId)
                ->where('pump_id', $pump->id)
                ->where(function ($query) use ($date, $rotationOrder, $currentRotationIndex) {
                    $query->where(function ($q) use ($date, $rotationOrder, $currentRotationIndex) {
                        // Rotation du même jour, antérieure
                        $q->whereDate('date', $date)
                            ->whereIn('rotation', array_slice($rotationOrder, 0, $currentRotationIndex));
                    })->orWhere(function ($q) use ($date) {
                        // Rotation du jour précédent
                        $q->whereDate('date', '<', $date);
                    });
                })
                ->orderByDesc('date')
                ->orderByDesc(DB::raw("FIELD(rotation, '6-14', '14-22', '22-6')"))
                ->first();

            $lastIndexes[$pump->id] = $lastFuelIndex->index_fin ?? null;
        }

        return view('fuel_indexes.create', compact('pumps', 'lastIndexes', 'rotation', 'date'));
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
            'pumps.*.retour_en_cuve' => 'nullable|numeric|min:0',
        ]);

        $stationId = session('selected_station_id');
        $userId = auth()->id();

        // 🔒 Vérifie si une saisie existe déjà pour cette date + rotation
        $existing = FuelIndex::where('station_id', $stationId)
            ->whereDate('date', $request->date)
            ->where('rotation', $request->rotation)
            ->exists();

        if ($existing) {
            return back()->withErrors([
                'rotation' => 'Un relevé pour cette date et cette rotation existe déjà.',
            ])->withInput();
        }

        // ✅ Enregistre les relevés pour chaque pompe
        foreach ($request->pumps as $pumpData) {
            FuelIndex::create([
                'station_id' => $stationId,
                'pump_id' => $pumpData['pump_id'],
                'user_id' => $userId,
                'date' => $request->date,
                'rotation' => $request->rotation,
                'index_debut' => $pumpData['index_debut'],
                'index_fin' => $pumpData['index_fin'],
                'retour_en_cuve' => $pumpData['retour_en_cuve'] ?? 0,
                'prix_unitaire' => $pumpData['prix_unitaire'],
                'montant_recette' => (($pumpData['index_fin'] - $pumpData['index_debut']) - $pumpData['retour_en_cuve'] ?? 0) * $pumpData['prix_unitaire'],
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

    public function edit(FuelIndex $fuelIndex)
    {
        $stationId = $fuelIndex->station_id;

        $isValidated = DailyRevenueValidation::where('station_id', $fuelIndex->station_id)
            ->whereDate('date', $fuelIndex->date)
            ->where('rotation', $fuelIndex->rotation)
            ->exists();

        if ($isValidated) {
            return redirect()->route('fuel-indexes.details', [
                'date' => $fuelIndex->date->format('Y-m-d'),
                'rotation' => $fuelIndex->rotation,
            ])->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }

        return view('fuel_indexes.edit', compact('fuelIndex'));
    }

    public function update(Request $request, FuelIndex $fuelIndex)
    {
        // Vérifie que la rotation n’est pas validée
        $isValidated = DailyRevenueValidation::where('station_id', $fuelIndex->station_id)
            ->whereDate('date', $fuelIndex->date)
            ->where('rotation', $fuelIndex->rotation)
            ->exists();

        if ($isValidated) {
            return redirect()->route('fuel-indexes.details', [
                'date' => $fuelIndex->date->format('Y-m-d'),
                'rotation' => $fuelIndex->rotation,
            ])->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }

        // Valide uniquement les champs modifiables
        $pumpData = $request->validate([
            'index_debut' => 'required|numeric|min:0',
            'index_fin' => 'required|numeric|gte:index_debut',
            'retour_en_cuve' => 'nullable|numeric|min:0',
        ]);

        // Met à jour sans toucher au prix unitaire
        $fuelIndex->update([
            'index_debut' => $request->index_debut,
            'index_fin' => $request->index_fin,
            'retour_en_cuve' => $request->retour_en_cuve ?? 0,
            'montant_recette' => (($pumpData['index_fin'] - $pumpData['index_debut']) - $pumpData['retour_en_cuve'] ?? 0) * $fuelIndex->prix_unitaire,
        ]);

        return redirect()->route('fuel-indexes.details', [
            'date' => $fuelIndex->date->format('Y-m-d'),
            'rotation' => $fuelIndex->rotation,
        ])->with('success', 'Relevé mis à jour avec succès.');
    }

}
