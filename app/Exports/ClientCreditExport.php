<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientCreditExport implements FromArray, WithHeadings
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->client->creditTopups()->with('payments')->get() as $credit) {
            $rows[] = [
                'Crédit du ' . $credit->date,
                $credit->amount,
                'Remboursé',
                $credit->payments->sum('amount'),
                'Reste à payer',
                $credit->amount - $credit->payments->sum('amount'),
            ];

            foreach ($credit->payments as $payment) {
                $rows[] = [
                    '→ Paiement le ' . $payment->date,
                    '',
                    '',
                    $payment->amount,
                    '',
                    ''
                ];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Événement', 'Montant du crédit', '', 'Montant payé', '', 'Reste dû'];
    }
}
