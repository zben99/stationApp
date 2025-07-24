<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\FuelIndex;
use App\Models\TankStock;
use Illuminate\Http\Request;
use App\Models\TankStockHistory;
use App\Traits\ValidatesRotation;
use Illuminate\Support\Facades\DB;

class FuelIndexController extends Controller
{
    use ValidatesRotation;

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

        $rotationOrder = ['6-14', '14-22', '22-6'];
        $currentRotationIndex = array_search($rotation, $rotationOrder);

        $lastIndexes = [];

        foreach ($pumps as $pump) {
            $lastFuelIndex = FuelIndex::where('station_id', $stationId)
                ->where('pump_id', $pump->id)
                ->where(function ($query) use ($date, $rotationOrder, $currentRotationIndex) {
                    $query->where(function ($q) use ($date, $rotationOrder, $currentRotationIndex) {
                        $q->whereDate('date', $date)
                            ->whereIn('rotation', array_slice($rotationOrder, 0, $currentRotationIndex));
                    })->orWhere(function ($q) use ($date) {
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

        $existing = FuelIndex::where('station_id', $stationId)
            ->whereDate('date', $request->date)
            ->where('rotation', $request->rotation)
            ->exists();

        if ($existing) {
            return back()->withErrors([
                'rotation' => 'Un relevé pour cette date et cette rotation existe déjà.',
            ])->withInput();
        }

        foreach ($request->pumps as $pumpData) {
            $pump = Pump::with('tank.stock')->findOrFail($pumpData['pump_id']);
            $tank = $pump->tank;

            $indexDebut = $pumpData['index_debut'];
            $indexFin = $pumpData['index_fin'];
            $retour = $pumpData['retour_en_cuve'] ?? 0;
            $vente = ($indexFin - $indexDebut) - $retour;
            $prix = $pumpData['prix_unitaire'];
            $montant = $vente * $prix;

            $fuelIndex=FuelIndex::create([
                'station_id' => $stationId,
                'pump_id' => $pump->id,
                'user_id' => $userId,
                'date' => $request->date,
                'rotation' => $request->rotation,
                'index_debut' => $indexDebut,
                'index_fin' => $indexFin,
                'retour_en_cuve' => $retour,
                'prix_unitaire' => $prix,
                'montant_recette' => $montant,
            ]);

            // Mise à jour du stock de la cuve
            if ($tank && $tank->stock) {
                $tank->stock->decrement('quantite_actuelle', $vente);

                TankStockHistory::create([
                    'station_id' => $stationId,
                    'tank_id' => $tank->id,
                    'previous_quantity' => $tank->stock->quantite_actuelle + $vente,
                    'change_quantity' => -$vente,
                    'new_quantity' => $tank->stock->quantite_actuelle,
                    'operation_type' => 'vente',
                    'operation_id' => $fuelIndex->id,
                    'operation_date' => $request->date . ' ' . now()->format('H:i:s'),
                ]);
            }


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

        if (! $this->modificationAllowed(
            $fuelIndex->station_id,
            $fuelIndex->date,
            $fuelIndex->rotation
        )) {
            return redirect()->route('fuel-indexes.details', [
                'date' => $fuelIndex->date->format('Y-m-d'),
                'rotation' => $fuelIndex->rotation,
            ])->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }

        return view('fuel_indexes.edit', compact('fuelIndex'));
    }

    public function update(Request $request, FuelIndex $fuelIndex)
    {
        if (! $this->modificationAllowed(
            $fuelIndex->station_id,
            $fuelIndex->date,
            $fuelIndex->rotation
        )) {
            return redirect()->route('fuel-indexes.details', [
                'date' => $fuelIndex->date->format('Y-m-d'),
                'rotation' => $fuelIndex->rotation,
            ])->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }

        $pumpData = $request->validate([
            'index_debut' => 'required|numeric|min:0',
            'index_fin' => 'required|numeric|gte:index_debut',
            'retour_en_cuve' => 'nullable|numeric|min:0',
        ]);

        $vente = ($pumpData['index_fin'] - $pumpData['index_debut']) - ($pumpData['retour_en_cuve'] ?? 0);

        $fuelIndex->update([
            'index_debut' => $request->index_debut,
            'index_fin' => $request->index_fin,
            'retour_en_cuve' => $request->retour_en_cuve ?? 0,
            'montant_recette' => $vente * $fuelIndex->prix_unitaire,
        ]);

        return redirect()->route('fuel-indexes.details', [
            'date' => $fuelIndex->date->format('Y-m-d'),
            'rotation' => $fuelIndex->rotation,
        ])->with('success', 'Relevé mis à jour avec succès.');
    }
}
