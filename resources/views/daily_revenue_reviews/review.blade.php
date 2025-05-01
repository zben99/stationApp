<x-app-layout>
    <x-slot name="header">Récapitulatif des recettes du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} — Rotation {{ $rotation }}</x-slot>

    <div class="mb-4">
        <h5>⛽ Carburant</h5>
        <table class="table table-sm table-bordered">
            <thead><tr><th>Pompe</th><th>Produit</th><th>Début</th><th>Fin</th><th>Quantité</th><th>Montant</th></tr></thead>
            <tbody>
                @foreach($fuelIndexes as $fuel)
                    <tr>
                        <td>{{ $fuel->pump->name }}</td>
                        <td>{{ $fuel->pump->tank->product->name }}</td>
                        <td>{{ $fuel->index_debut }}</td>
                        <td>{{ $fuel->index_fin }}</td>
                        <td>{{ $fuel->index_fin - $fuel->index_debut }}</td>
                        <td>{{ number_format(($fuel->index_fin - $fuel->index_debut) * $fuel->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h5>🛢️ Produits (Lubrifiants / PEA / Gaz / Lampes)</h5>
        <table class="table table-sm table-bordered">
            <thead><tr><th>Produit</th><th>Format</th><th>Quantité</th><th>PU</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($productSales as $sale)
                    <tr>
                        <td>{{ $sale->productPackaging->product->name ?? '-' }}</td>
                        <td>{{ $sale->productPackaging->packaging->label ?? '-' }}</td>
                        <td>{{ $sale->quantite }}</td>
                        <td>{{ $sale->prix_unitaire }}</td>
                        <td>{{ number_format($sale->quantite * $sale->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h5>🧼 Boutique / Lavage</h5>
        <ul>
            <li><strong>Boutique :</strong> {{ number_format($simpleRevenues['boutique']->amount ?? 0, 0, ',', ' ') }} FCFA</li>
            <li><strong>Lavage :</strong> {{ number_format($simpleRevenues['lavage']->amount ?? 0, 0, ',', ' ') }} FCFA</li>
        </ul>
    </div>

    <div class="text-end">
        <a href="#" class="btn btn-success">✅ Valider la recette complète</a>
    </div>
</x-app-layout>
