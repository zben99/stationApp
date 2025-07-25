<table border="1" cellspacing="0" cellpadding="5">
    <tr><th colspan="2"><strong>Recette consolidée - {{ $date }} | Rotation {{ $rotation }}</strong></th></tr>

    <tr><th colspan="2">Carburants</th></tr>
    <tr><td>Carburant Super</td><td>{{ number_format($validation->fuel_super_amount, 2) }}</td></tr>
    <tr><td>Carburant Gasoil</td><td>{{ number_format($validation->fuel_gazoil_amount, 2) }}</td></tr>

    <tr><th colspan="2">Produits vendus</th></tr>
    <tr><td>Lubrifiants</td><td>{{ number_format($validation->lub_amount, 2) }}</td></tr>
    <tr><td>PEA</td><td>{{ number_format($validation->pea_amount, 2) }}</td></tr>
    <tr><td>Gaz</td><td>{{ number_format($validation->gaz_amount, 2) }}</td></tr>
    <tr><td>Lampes</td><td>{{ number_format($validation->lampes_amount, 2) }}</td></tr>
    <tr><td>Divers</td><td>{{ number_format($validation->divers_amount, 2) }}</td></tr>
    <tr><td>Lavage</td><td>{{ number_format($validation->lavage_amount, 2) }}</td></tr>
    <tr><td>Boutique</td><td>{{ number_format($validation->boutique_amount, 2) }}</td></tr>

    <tr><th colspan="2">Crédits & Soldes</th></tr>
    <tr><td>Crédit reçu</td><td>{{ number_format($validation->credit_received, 2) }}</td></tr>
    <tr><td>Remboursement crédit</td><td>{{ number_format($validation->credit_repaid, 2) }}</td></tr>
    <tr><td>Recharge solde</td><td>{{ number_format($validation->balance_received, 2) }}</td></tr>
    <tr><td>Solde utilisé</td><td>{{ number_format($validation->balance_used, 2) }}</td></tr>

    <tr><th colspan="2">Sorties</th></tr>
    <tr><td>Dépenses</td><td>{{ number_format($validation->expenses, 2) }}</td></tr>
    <tr><td>Factures payées</td><td>{{ number_format($validation->payment_facture, 2) }}</td></tr>

    <tr><th colspan="2">Résumé</th></tr>
    <tr><td>Montant en caisse</td><td>{{ number_format($validation->cash_amount, 2) }}</td></tr>
    <tr><td>Écart caisse / net</td><td>{{ number_format($validation->cash_amount - $validation->net_to_deposit, 2) }}</td></tr>
    <tr><td><strong>Montant net à déposer</strong></td><td><strong>{{ number_format($validation->net_to_deposit, 2) }}</strong></td></tr>
</table>
