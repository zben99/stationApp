
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        h1, h2, h3 {
            text-align: center;
            margin: 0 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .section-title {
            background-color: #dce6f1;
            font-weight: bold;
        }
        .total-row {
            background-color: #e2f0d9;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <h2>Recette Journalière Consolidée</h2>
    <p><strong>Date :</strong> {{ $validation->date }} &nbsp;&nbsp;&nbsp; <strong>Rotation :</strong> {{ $validation->rotation }}</p>

    @php
        $totalFuel = $validation->fuel_super_amount + $validation->fuel_gazoil_amount;
        $totalProduits = $validation->lub_amount + $validation->pea_amount + $validation->gaz_amount + $validation->lampes_amount + $validation->divers_amount + $validation->lavage_amount + $validation->boutique_amount;
        $totalCredits = $validation->credit_received + $validation->credit_repaid + $validation->balance_received + $validation->balance_used;
        $totalSorties = $validation->expenses + $validation->payment_facture;
    @endphp

    <table>
        <tr><th colspan="2" class="section-title">Carburants</th></tr>
        <tr><td>Super</td><td>{{ number_format($validation->fuel_super_amount, 2) }} FCFA</td></tr>
        <tr><td>Gasoil</td><td>{{ number_format($validation->fuel_gazoil_amount, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Total Carburants</td><td>{{ number_format($totalFuel, 2) }} FCFA</td></tr>

        <tr><th colspan="2" class="section-title">Produits</th></tr>
        <tr><td>Lubrifiants</td><td>{{ number_format($validation->lub_amount, 2) }} FCFA</td></tr>
        <tr><td>PEA</td><td>{{ number_format($validation->pea_amount, 2) }} FCFA</td></tr>
        <tr><td>Gaz</td><td>{{ number_format($validation->gaz_amount, 2) }} FCFA</td></tr>
        <tr><td>Lampes</td><td>{{ number_format($validation->lampes_amount, 2) }} FCFA</td></tr>
        <tr><td>Divers</td><td>{{ number_format($validation->divers_amount, 2) }} FCFA</td></tr>
        <tr><td>Lavage</td><td>{{ number_format($validation->lavage_amount, 2) }} FCFA</td></tr>
        <tr><td>Boutique</td><td>{{ number_format($validation->boutique_amount, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Total Produits</td><td>{{ number_format($totalProduits, 2) }} FCFA</td></tr>

        <tr><th colspan="2" class="section-title">Crédits & Avoirs</th></tr>
        <tr><td>Crédit reçu</td><td>{{ number_format($validation->credit_received, 2) }} FCFA</td></tr>
        <tr><td>Remboursement crédit</td><td>{{ number_format($validation->credit_repaid, 2) }} FCFA</td></tr>
        <tr><td>Recharge solde</td><td>{{ number_format($validation->balance_received, 2) }} FCFA</td></tr>
        <tr><td>Avoir perçu</td><td>{{ number_format($validation->balance_used, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Total Crédits/Avoirs</td><td>{{ number_format($totalCredits, 2) }} FCFA</td></tr>

        <tr><th colspan="2" class="section-title">Moyens de Paiement</th></tr>
        <tr><td>OM</td><td>{{ number_format($validation->om_amount, 2) }} FCFA</td></tr>
        <tr><td>OM Recharge</td><td>{{ number_format($validation->om_recharge_amount, 2) }} FCFA</td></tr>
        <tr><td>TPE</td><td>{{ number_format($validation->tpe_amount, 2) }} FCFA</td></tr>
        <tr><td>TPE Recharge</td><td>{{ number_format($validation->tpe_recharge_amount, 2) }} FCFA</td></tr>

        <tr><th colspan="2" class="section-title">Sorties & Caisse</th></tr>
        <tr><td>Dépenses</td><td>{{ number_format($validation->expenses, 2) }} FCFA</td></tr>
        <tr><td>Factures payées</td><td>{{ number_format($validation->payment_facture, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Total Sorties</td><td>{{ number_format($totalSorties, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Montant en caisse (déclaré)</td><td>{{ number_format($validation->cash_amount, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Montant net à déposer</td><td>{{ number_format($validation->net_to_deposit, 2) }} FCFA</td></tr>
        <tr class="total-row"><td>Écart</td><td>{{ number_format($validation->cash_amount - $validation->net_to_deposit, 2) }} FCFA</td></tr>
    </table>

    <div class="footer">
        Généré automatiquement le {{ now()->format('d/m/Y à H:i') }}.
    </div>
</body>
</html>
