<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Fiche de Dépotage</h2>

    <table>
        <tr><th>Date</th><td>{{ \Carbon\Carbon::parse($reception->date_reception)->format('d/m/Y') }}</td></tr>
        <tr><th>Station</th><td>{{ $reception->station->name ?? '-' }} {{ $reception->station->location ?? '-' }}</td></tr>
        <tr><th>Transporteur</th><td>{{ $reception->transporter->name ?? '-' }}</td></tr>
        <tr><th>Chauffeur</th><td>{{ $reception->driver->name ?? '-' }}</td></tr>
        <tr><th>Bon de livraison</th><td>{{ $reception->num_bl ?? '-' }}</td></tr>
        <tr><th>Remarques</th><td>{{ $reception->remarques ?? '-' }}</td></tr>
    </table>

    <h4>Détail des Cuves</h4>
    <table>
        <thead>
            <tr>
                <th>Cuve</th>
                <th>Produit</th>
                <th>Jauge Avant</th>
                <th>Qté Reçue</th>
                <th>Jauge Après</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reception->lines as $line)
                <tr>
                    <td>{{ $line->tank->code ?? '-' }}</td>
                    <td>{{ $line->tank->product->name ?? '-' }}</td>
                    <td>{{ $line->jauge_avant ?? '-' }}</td>
                    <td>{{ $line->reception_par_cuve ?? '-' }}</td>
                    <td>{{ $line->jauge_apres ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <br><br>
    <table style="width: 100%; margin-top: 40px; border: none;">
        <tr style="border: none;">
            <td style="width: 50%; border: none; text-align: center;">
                <strong>Signature du Responsable Station</strong><br><br><br><br><br><br>
                ..................................................<br>
                Nom, Prénom et cachet
            </td>
            <td style="width: 50%; border: none; text-align: center;">
                <strong>Signature du Transporteur / Chauffeur</strong><br><br><br><br><br><br>
                ..................................................<br>
                Nom et prénom
            </td>
        </tr>
    </table>

</body>
</html>
