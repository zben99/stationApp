<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État cumulé dépotages carburant</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            margin: 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Dépotages carburant – État cumulé</h2>
    <p><strong>Période :</strong> {{ $start }} au {{ $end }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit | Cuve</th>
                <th>Total dépoté (L)</th>
                <th>Nombre de livraisons</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($grouped as $key => $group)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ number_format($group->sum('reception_par_cuve'), 2) }}</td>
                    <td>{{ $group->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Aucune donnée disponible pour cette période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
