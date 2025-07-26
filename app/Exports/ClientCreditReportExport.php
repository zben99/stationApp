<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientCreditReportExport implements FromCollection, WithHeadings
{
    protected $stationId, $clientId;

    public function __construct($stationId, $clientId = null)
    {
        $this->stationId = $stationId;
        $this->clientId = $clientId;
    }

    public function collection()
    {
        $clientsQuery = Client::with(['creditTopups', 'creditPayments'])
            ->where('station_id', $this->stationId);

        if ($this->clientId) {
            $clientsQuery->where('id', $this->clientId);
        }

        return $clientsQuery->get()->map(function ($client) {
            $totalCredit = $client->creditTopups->sum('amount');
            $totalRepayment = $client->creditPayments->sum('amount');
            $solde = $totalCredit - $totalRepayment;

            return [
                'Nom client' => $client->name,
                'Téléphone' => $client->phone,
                'Crédit reçu' => $totalCredit,
                'Remboursé' => $totalRepayment,
                'Solde restant' => $solde,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nom client', 'Téléphone', 'Crédit reçu', 'Remboursé', 'Solde restant'];
    }
}
