<table>
    <thead>
        <tr>
            <th>Pompiste</th>
            <th>Pompe</th>
            <th>Produit</th>
            <th>Index Début</th>
            <th>Index Fin</th>
            <th>Retour Cuve</th>
            <th>Vente</th>
            <th>Prix Unitaire</th>
            <th>Montant</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fuelIndexes as $index)
            <tr>
                <td>{{ $index->user->name ?? '-' }}</td>
                <td>{{ $index->pump->name ?? '-' }}</td>
                <td>{{ $index->pump->tank->product->name ?? '-' }}</td>
                <td>{{ number_format($index->index_debut, 2) }}</td>
                <td>{{ number_format($index->index_fin, 2) }}</td>
                <td>{{ number_format($index->retour_en_cuve, 2) }}</td>
                <td>{{ number_format(($index->index_fin - $index->index_debut - $index->retour_en_cuve), 2) }}</td>
                <td>{{ number_format($index->prix_unitaire, 2) }}</td>
                <td>{{ number_format($index->montant_recette, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
