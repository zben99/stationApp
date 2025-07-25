<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h3>Recettes consolidées du {{ $start }} au {{ $end }}</h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Super</th>
                <th>Gasoil</th>
                <th>Lub</th>
                <th>PEA</th>
                <th>Gaz</th>
                <th>Boutique</th>
                <th>Crédit</th>
                <th>Solde</th>
                <th>Dépenses</th>
                <th>Facture</th>
                <th>Net à déposer</th>
            </tr>
        </thead>
        <tbody>
            @foreach($validations as $v)
                <tr>
                    <td>{{ $v->date }}</td>
                    <td>{{ $v->rotation }}</td>
                    <td>{{ number_format($v->fuel_super_amount, 0) }}</td>
                    <td>{{ number_format($v->fuel_gazoil_amount, 0) }}</td>
                    <td>{{ number_format($v->lub_amount, 0) }}</td>
                    <td>{{ number_format($v->pea_amount, 0) }}</td>
                    <td>{{ number_format($v->gaz_amount, 0) }}</td>
                    <td>{{ number_format($v->boutique_amount, 0) }}</td>
                    <td>{{ number_format($v->credit_received, 0) }}</td>
                    <td>{{ number_format($v->balance_received, 0) }}</td>
                    <td>{{ number_format($v->expenses, 0) }}</td>
                    <td>{{ number_format($v->payment_facture, 0) }}</td>
                    <td><strong>{{ number_format($v->net_to_deposit, 0) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
