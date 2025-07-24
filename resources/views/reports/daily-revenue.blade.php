<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">Recette journalière consolidée</h2>
    </x-slot>

    <div class="py-4 container">
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-4">
                <select name="rotation" class="form-control">
                    <option value="6-14" {{ $rotation == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ $rotation == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6" {{ $rotation == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Afficher</button>
            </div>
        </form>

        @if($validation)
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th>Carburant Super</th><td>{{ number_format($validation->fuel_super_amount, 2) }} FCFA</td></tr>
                            <tr><th>Carburant Gasoil</th><td>{{ number_format($validation->fuel_gazoil_amount, 2) }} FCFA</td></tr>
                            <tr><th>Lubrifiants</th><td>{{ number_format($validation->lub_amount, 2) }} FCFA</td></tr>
                            <tr><th>Produits d'entretien auto</th><td>{{ number_format($validation->pea_amount, 2) }} FCFA</td></tr>
                            <tr><th>Gaz</th><td>{{ number_format($validation->gaz_amount, 2) }} FCFA</td></tr>
                            <tr><th>Lampes</th><td>{{ number_format($validation->lampes_amount, 2) }} FCFA</td></tr>
                            <tr><th>Divers</th><td>{{ number_format($validation->divers_amount, 2) }} FCFA</td></tr>
                            <tr><th>Lavage</th><td>{{ number_format($validation->lavage_amount, 2) }} FCFA</td></tr>
                            <tr><th>Boutique</th><td>{{ number_format($validation->boutique_amount, 2) }} FCFA</td></tr>
                            <tr><th>Remboursement crédit</th><td>{{ number_format($validation->credit_repaid, 2) }} FCFA</td></tr>
                            <tr><th>Recharge solde</th><td>{{ number_format($validation->balance_received, 2) }} FCFA</td></tr>
                            <tr><th>Avoir perçu</th><td>{{ number_format($validation->balance_used, 2) }} FCFA</td></tr>
                            <tr><th>Crédit reçu</th><td>{{ number_format($validation->credit_received, 2) }} FCFA</td></tr>
                            <tr><th>Dépenses</th><td>{{ number_format($validation->expenses, 2) }} FCFA</td></tr>
                            <tr><th>Factures payées</th><td>{{ number_format($validation->payment_facture, 2) }} FCFA</td></tr>
                            <tr class="table-success">
                                <th><strong>Montant net à déposer</strong></th>
                                <td><strong>{{ number_format($validation->net_to_deposit, 2) }} FCFA</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                Aucun enregistrement trouvé pour cette date et cette rotation.
            </div>
        @endif
    </div>


        <a href="{{ route('repports.index') }}" class="btn btn-secondary mt-3">Retour</a>

</x-app-layout>
