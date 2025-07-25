<?php

namespace App\Exports;

use App\Models\DailyRevenueValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConsolidatedPeriodExport implements FromCollection, WithHeadings
{
    protected $stationId;
    protected $startDate;
    protected $endDate;

    public function __construct($stationId, $startDate, $endDate)
    {
        $this->stationId = $stationId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Collection
    {
        return DailyRevenueValidation::where('station_id', $this->stationId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date')
            ->orderByRaw("FIELD(rotation, '6-14', '14-22', '22-6')")
            ->get()
            ->map(function ($v) {
                return [
                    $v->date,
                    $v->rotation,
                    $v->fuel_super_amount,
                    $v->fuel_gazoil_amount,
                    $v->lub_amount,
                    $v->pea_amount,
                    $v->gaz_amount,
                    $v->boutique_amount,
                    $v->credit_received,
                    $v->balance_received,
                    $v->expenses,
                    $v->payment_facture,
                    $v->net_to_deposit,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Rotation',
            'Super',
            'Gasoil',
            'Lubrifiants',
            'PEA',
            'Gaz',
            'Boutique',
            'Crédit reçu',
            'Solde reçu',
            'Dépenses',
            'Factures payées',
            'Net à déposer',
        ];
    }
}
