<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Approvisionnement Carburant</title>
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

    <h2>Rapport d'approvisionnement carburant</h2>
    <p><strong>Période :</strong> {{ $start }} au {{ $end }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Produit</th>
                <th>Cuve</th>
                <th>Transporteur</th>
                <th>BL</th>
                <th>Qté reçue (L)</th>
                <th>Prix achat (F)</th>

                <th>Total achat (F)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($receptions as $reception)
                @foreach ($reception->lines as $line)
                    <tr>
                        <td>{{ $reception->date_reception->format('Y-m-d') }}</td>
                        <td>{{ $reception->rotation }}</td>
                        <td>{{ $line->tank->product->name ?? '-' }}</td>
                        <td>{{ $line->tank->name ?? '-' }}</td>
                        <td>{{ $reception->transporter->name ?? '-' }}</td>
                          <td>{{ $reception->num_bl }}</td>
                        <td>{{ number_format($line->reception_par_cuve, 2) }}</td>
                        <td>{{ number_format($line->unit_price_purchase, 2) }}</td>

                        <td>{{ number_format($line->reception_par_cuve * $line->unit_price_purchase, 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" class="text-center">Aucun approvisionnement trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
