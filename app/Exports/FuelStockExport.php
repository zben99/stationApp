<?php

namespace App\Exports;

use App\Models\Pump;
use App\Models\Tank;
use App\Models\Station;
use App\Models\FuelIndex;
use App\Models\FuelReceptionLine;
use App\Models\TankPhysicalStock;
use App\Models\FuelStockControl;
use App\Models\TankStockHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FuelStockExport implements FromCollection, WithHeadings
{
    protected string $from;
    protected string $to;
    protected int $stationId;
    protected bool $forceRecalculate;

    public function __construct(string $from, string $to, int $stationId, bool $forceRecalculate = false)
    {
        $this->from = $from;
        $this->to = $to;
        $this->stationId = $stationId;
        $this->forceRecalculate = $forceRecalculate;
    }

    public function collection()
    {
        $rows = collect();

        $station = Station::find($this->stationId);
        $stationName = $station?->name ?? 'Station inconnue';

        // Titre
        $rows->push(["Contrôle de stock carburants"]);
        $rows->push(["Station : {$stationName}"]);
        $rows->push(["Période : du {$this->from} au {$this->to}"]);
        $rows->push([]);

        // En-tête
        $rows->push([
            'CUVES', 'PRODUITS', 'INDEX Fermeture', 'INDEX Ouverture',
            'Différence d’index', 'RETOUR EN CUVE', 'VENTE',
            'Stock Ouverture', 'Livraison Période', 'Stock physique',
            'Stock théorique', 'écart sur stock', 'écart en %'
        ]);

        $tanks = Tank::where('station_id', $this->stationId)->with(['product', 'stock'])->get();

        foreach ($tanks as $tank) {
            // Si snapshot existe et pas de recalcul forcé
            $snapshot = FuelStockControl::where('station_id', $this->stationId)
                ->where('tank_id', $tank->id)
                ->where('control_date', $this->to)
                ->first();

            if ($snapshot && !$this->forceRecalculate) {
                $rows->push([
                    $tank->code,
                    $tank->product->name ?? '-',
                    number_format($snapshot->index_end, 2, '.', ''),
                    number_format($snapshot->index_start, 2, '.', ''),
                    number_format($snapshot->index_end - $snapshot->index_start, 2, '.', ''),
                    number_format($snapshot->return_to_tank, 2, '.', ''),
                    number_format($snapshot->sale, 2, '.', ''),
                    number_format($snapshot->stock_opening, 2, '.', ''),
                    number_format($snapshot->reception, 2, '.', ''),
                    number_format($snapshot->stock_physical, 2, '.', ''),
                    number_format($snapshot->stock_theoretical, 2, '.', ''),
                    number_format($snapshot->gap_liters, 2, '.', ''),
                    number_format($snapshot->gap_percent, 2, '.', '') . '%',
                ]);
                continue;
            }

            // Recalcul automatique
            $pumpIds = Pump::where('tank_id', $tank->id)->pluck('id');
            $indexes = FuelIndex::where('station_id', $this->stationId)
                ->whereIn('pump_id', $pumpIds)
                ->whereBetween('date', [$this->from, $this->to])
                ->get();

            $indexDebut  = (float) $indexes->sum('index_debut');
            $indexFin    = (float) $indexes->sum('index_fin');
            $retourCuve  = (float) $indexes->sum('retour_en_cuve');
            $diffIndex   = $indexFin - $indexDebut;
            $vente       = $diffIndex - $retourCuve;

            $reception = (float) FuelReceptionLine::where('tank_id', $tank->id)
                ->whereHas('reception', function ($q) {
                    $q->where('station_id', $this->stationId)
                      ->whereBetween('date_reception', [$this->from, $this->to]);
                })
                ->sum('reception_par_cuve');

            // 1. Vérifie s’il existe un stock physique saisi manuellement
            $manualStock = TankPhysicalStock::where('tank_id', $tank->id)
                ->where('station_id', $this->stationId)
                ->where('date', $this->to)
                ->value('quantity');

            // 2. Sinon, cherche le dernier stock historique
            if (is_numeric($manualStock)) {
                $stockPhysique = (float) $manualStock;
            } else {
                $lastStock = TankStockHistory::where('station_id', $this->stationId)
                    ->where('tank_id', $tank->id)
                    ->where('operation_date', '<=', $this->to . ' 23:59:59')
                    ->orderByDesc('operation_date')
                    ->value('new_quantity');

                $stockPhysique = (float) ($lastStock ?? 0);
            }




            // ✅ Stock d’ouverture à la date du $from
            $stockOuverture = (float) TankStockHistory::where('station_id', $this->stationId)
            ->where('tank_id', $tank->id)
            ->where('operation_date', '<', $this->from . ' 00:00:00') // ✅ AVANT la période
            ->orderByDesc('operation_date')
            ->value('new_quantity') ?? 0;

             $stockTheorique = $stockOuverture + $reception - $vente;



            $ecartL = $stockPhysique - $stockTheorique;
            $ecartPercent = $vente > 0 ? round(($ecartL / $vente) * 100, 2) : 0;

            // Sauvegarde snapshot
            FuelStockControl::updateOrCreate([
                'station_id' => $this->stationId,
                'tank_id' => $tank->id,
                'control_date' => $this->to,
            ], [
                'stock_opening' => $stockOuverture,
                'index_start' => $indexDebut,
                'index_end' => $indexFin,
                'return_to_tank' => $retourCuve,
                'sale' => $vente,
                'reception' => $reception,
                'stock_theoretical' => $stockTheorique,
                'stock_physical' => $stockPhysique,
                'gap_liters' => $ecartL,
                'gap_percent' => $ecartPercent,
            ]);

            $rows->push([
                $tank->code,
                $tank->product->name ?? '-',
                number_format($indexFin, 2, '.', ''),
                number_format($indexDebut, 2, '.', ''),
                number_format($diffIndex, 2, '.', ''),
                number_format($retourCuve, 2, '.', ''),
                number_format($vente, 2, '.', ''),
                number_format($stockOuverture, 2, '.', ''),
                number_format($reception, 2, '.', ''),
                number_format($stockPhysique, 2, '.', ''),
                number_format($stockTheorique, 2, '.', ''),
                number_format($ecartL, 2, '.', ''),
                $ecartPercent . '%',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }
}
