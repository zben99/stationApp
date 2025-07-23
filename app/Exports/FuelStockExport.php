<?php

namespace App\Exports;

use App\Models\FuelIndex;
use App\Models\FuelReceptionLine;
use App\Models\Pump;
use App\Models\Tank;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FuelStockExport implements FromCollection, WithHeadings
{
    protected $from;
    protected $to;
    protected $stationId;

    public function __construct(string $from, string $to, int $stationId)
    {
        $this->from = $from;
        $this->to = $to;
        $this->stationId = $stationId;
    }

    public function collection()
    {
        $rows = collect();

        $tanks = Tank::where('station_id', $this->stationId)
            ->with(['product', 'stock'])
            ->get();

        foreach ($tanks as $tank) {
            $pumpIds = Pump::where('tank_id', $tank->id)->pluck('id');

            $indexes = FuelIndex::where('station_id', $this->stationId)
                ->whereIn('pump_id', $pumpIds)
                ->whereBetween('date', [$this->from, $this->to])
                ->get();

            $indexDebut = $indexes->sum('index_debut');
            $indexFin = $indexes->sum('index_fin');
            $retourCuve = $indexes->sum('retour_en_cuve');
            $vente = $indexFin - $indexDebut - $retourCuve;

            $receptionQuery = FuelReceptionLine::where('tank_id', $tank->id)
                ->whereHas('reception', function ($q) {
                    $q->where('station_id', $this->stationId)
                      ->whereBetween('date_reception', [$this->from, $this->to]);
                });

            $reception = $receptionQuery->sum('reception_par_cuve');
            $stockPhysique = $receptionQuery->sum('station_d15');

            $stockOuverture = $tank->stock->quantite_actuelle ?? 0;
            $stockTheorique = $stockOuverture + $reception - $vente;
            $ecartL = $stockPhysique - $stockTheorique;
            $ecartPercent = $vente > 0 ? ($ecartL / $vente) * 100 : 0;

            $rows->push([
                $tank->code,
                $tank->product->name ?? '-',
                $indexDebut,
                $indexFin,
                $retourCuve,
                $vente,
                $stockOuverture,
                $reception,
                $stockTheorique,
                $stockPhysique,
                $ecartL,
                $ecartPercent,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Cuve',
            'Produit',
            'Index Début',
            'Index Fin',
            'Retour cuve',
            'Vente',
            'Stock ouverture',
            'Réception',
            'Stock théorique',
            'Stock physique',
            'Écart (L)',
            'Écart (%)',
        ];
    }
}
