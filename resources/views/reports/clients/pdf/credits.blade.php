<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Crédits Clients</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px 4px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .text-red {
            color: red;
        }
    </style>
</head>
<body>

    <h2>Rapport des Crédits Clients</h2>

    <table>
        <thead>
            <tr>
                <th>Nom client</th>
                <th>Téléphone</th>
                <th>Crédit reçu (F)</th>
                <th>Remboursé (F)</th>
                <th>Solde restant (F)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clients as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ number_format($c->credit, 2) }}</td>
                    <td>{{ number_format($c->repayment, 2) }}</td>
                    <td class="{{ $c->balance < 0 ? 'text-red' : '' }}">
                        {{ number_format($c->balance, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun client trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
