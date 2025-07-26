<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Approvisionnement Lubrifiants</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
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
            border: 1px solid #222;
            padding: 5px;
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

    <h2>Rapport d'approvisionnement Lubrifiant / PEA / GAZ / Lampes / Divers</h2>
    <p><strong>Période :</strong> {{ $start }} au {{ $end }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Produit</th>
                <th>Conditionnement</th>
                <th>Fournisseur</th>
                <th>Quantité</th>
                <th>Prix unitaire (F)</th>
                <th>Total (F)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batches as $batch)
                @foreach ($batch->receptions as $line)
                    <tr>
                        <td>{{ $batch->date_reception->format('Y-m-d') }}</td>
                        <td>{{ $batch->rotation }}</td>
                        <td>{{ $line->packaging->product->name ?? '-' }}</td>
                        <td>{{ $line->packaging->packaging->label ?? '-' }}</td>
                        <td>{{ $batch->supplier->name ?? '-' }}</td>
                        <td>{{ $line->quantite }}</td>
                        <td>{{ number_format($line->prix_achat, 2) }}</td>
                        <td>{{ number_format($line->quantite * $line->prix_achat, 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="text-center">Aucun approvisionnement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
