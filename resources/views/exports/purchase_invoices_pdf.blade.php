<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Factures d'achat</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #cfe2f3; }
    </style>
</head>
<body>
    <h2>Factures d'achat</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Numéro</th>
                <th>Fournisseur</th>
                <th>Valeur HT</th>
                <th>Valeur TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->date }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->supplier_name }}</td>
                    <td>{{ number_format($invoice->amount_ht, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($invoice->amount_ttc, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">Total HT</th>
                <th>
                    {{ number_format($invoices->sum('amount_ht'), 0, ',', ' ') }} F
                </th>
            </tr>
            <tr>
                <th colspan="4" style="text-align: right;">Total TTC</th>
                <th>
                    {{ number_format($invoices->sum('amount_ttc'), 0, ',', ' ') }} F
                </th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
