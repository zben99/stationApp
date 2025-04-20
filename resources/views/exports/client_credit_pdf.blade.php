<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique Crédit - {{ $client->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <h2>Historique de crédit du client : {{ $client->name }}</h2>

    @foreach($creditTopups as $credit)
        <h4>Crédit du {{ $credit->date }} - {{ number_format($credit->amount, 0, ',', ' ') }} F</h4>
        <p>
            Total remboursé : {{ number_format($credit->payments->sum('amount'), 0, ',', ' ') }} F <br>
            Reste dû : {{ number_format($credit->amount - $credit->payments->sum('amount'), 0, ',', ' ') }} F
        </p>

        @if($credit->payments->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($credit->payments as $payment)
                        <tr>
                            <td>{{ $payment->date }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} F</td>
                            <td>{{ $payment->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p><em>Aucun remboursement</em></p>
        @endif
        <hr>
    @endforeach
</body>
</html>
