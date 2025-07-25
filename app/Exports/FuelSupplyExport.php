<?php

namespace App\Exports;

use App\Models\FuelReception;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FuelSupplyExport implements FromCollection, WithHeadings
{
    protected $stationId, $start, $end;

    public function __construct($stationId, $start, $end)
    {
        $this->stationId = $stationId;
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $receptions = FuelReception::with(['lines.tank.product', 'transporter'])
            ->where('station_id', $this->stationId)
            ->whereBetween('date_reception', [$this->start, $this->end])
            ->orderByDesc('date_reception')
            ->get();

        $rows = collect();

        foreach ($receptions as $reception) {
            foreach ($reception->lines as $line) {
                $rows->push([
                    'Date' => $reception->date_reception->format('Y-m-d'),
                    'Rotation' => $reception->rotation,
                    'Produit' => $line->tank->product->name ?? '',
                    'Cuve' => $line->tank->name ?? '',
                    'Transporteur' => $reception->transporter->name ?? '',
                    'Qté reçue (L)' => $line->reception_par_cuve,
                    'Prix achat' => $line->unit_price_purchase,
                    'Montant total (achat)' => $line->reception_par_cuve * $line->unit_price_purchase,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Rotation',
            'Produit',
            'Cuve',
            'Transporteur',
            'Qté reçue (L)',
            'Prix achat',
            'Montant total (achat)',
        ];
    }
}
