<?php

namespace App\Exports;

use App\Models\FuelReceptionLine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FuelDepotageCumuleExport implements FromCollection, WithHeadings
{
    protected $stationId, $start, $end, $productId;

    public function __construct($stationId, $start, $end, $productId = null)
    {
        $this->stationId = $stationId;
        $this->start = $start;
        $this->end = $end;
        $this->productId = $productId;
    }

    public function collection()
    {
        $lines = FuelReceptionLine::with(['tank.product', 'reception'])
            ->whereHas('reception', function ($q) {
                $q->where('station_id', $this->stationId)
                  ->whereBetween('date_reception', [$this->start, $this->end]);
            })
            ->when($this->productId, function ($q) {
                $q->whereHas('tank.product', function ($q) {
                    $q->where('id', $this->productId);
                });
            })
            ->get();

        $grouped = $lines->groupBy(function ($line) {
            return $line->tank->product->name . ' | ' . $line->tank->code;
        });

        $data = new Collection();

        foreach ($grouped as $key => $group) {
            $data->push([
                'Produit | Cuve' => $key,
                'Total dépoté (L)' => $group->sum('reception_par_cuve'),
                'Nombre de livraisons' => $group->count(),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Produit | Cuve',
            'Total dépoté (L)',
            'Nombre de livraisons',
        ];
    }
}
