<?php

namespace App\Exports;

use App\Models\FuelStockControl;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FuelStockControlExport implements FromCollection, WithHeadings
{
    protected $stationId;
    protected $from;
    protected $to;

    public function __construct($stationId, $from, $to)
    {
        $this->stationId = $stationId;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        return FuelStockControl::with(['tank.product'])
            ->where('station_id', $this->stationId)
            ->when($this->from, fn($q) => $q->whereDate('control_date', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('control_date', '<=', $this->to))
            ->get()
            ->map(function ($item) {
                return [
                    'Date'            => $item->control_date,
                    'Cuve'            => $item->tank->code ?? '-',
                    'Produit'         => $item->tank->product->name ?? '-',
                    'Stock ouverture' => $item->stock_opening,
                    'Réception'       => $item->reception,
                    'Vente'           => $item->sale,
                    'Stock théorique' => $item->stock_theoretical,
                    'Stock physique'  => $item->stock_physical,
                    'Écart (L)'       => $item->gap_liters,
                    'Écart (%)'       => $item->gap_percent,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Cuve',
            'Produit',
            'Stock ouverture',
            'Réception',
            'Vente',
            'Stock théorique',
            'Stock physique',
            'Écart (L)',
            'Écart (%)',
        ];
    }
}
