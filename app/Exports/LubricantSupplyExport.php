<?php

namespace App\Exports;

use App\Models\LubricantReceptionBatch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LubricantSupplyExport implements FromCollection, WithHeadings
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
         $batches = LubricantReceptionBatch::with('supplier', 'receptions.packaging.product', 'receptions.packaging.packaging')
            ->where('station_id', $this->stationId)
            ->whereBetween('date_reception', [$this->start, $this->end]) // ← ici
            ->orderByDesc('date_reception')
            ->get();



        $rows = collect();

        foreach ($batches as $batch) {
            foreach ($batch->receptions as $line) {
                $rows->push([
                    'Date' => $batch->date_reception->format('Y-m-d'),
                    'Rotation' => $batch->rotation,
                    'Produit' => $line->packaging->product->name ?? '',
                    'Conditionnement' => $line->packaging->packaging->label ?? '',
                    'Fournisseur' => $batch->supplier->name ?? '',
                    'Quantité' => $line->quantite,
                    'Prix unitaire' => $line->unit_price,
                    'Montant total' => $line->quantite * $line->unit_price,
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
            'Conditionnement',
            'Fournisseur',
            'Quantité',
            'Prix unitaire',
            'Montant total',
        ];
    }
}
