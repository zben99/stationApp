<x-app-layout>
    <x-slot name="header">
        Détails relevé du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} (Rotation {{ $rotation }})
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('fuel-indexes.index') }}" class="btn btn-secondary">← Retour à la liste</a>
    </div>

    @php
        $totalQuantite = 0;
        $totalMontant = 0;
    @endphp

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Pompe</th>
                <th>Produit</th>
                <th>Pompiste</th>
                <th>Index début</th>
                <th>Index fin</th>
                <th>Retour cuve</th>
                <th>Qté (L)</th>
                <th>PU</th>
                <th>Montant</th>
                <th></th> <!-- Colonne pour actions -->
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                @php
                    $retour = $entry->retour_en_cuve ?? 0;
                    $qte = $entry->index_fin - $entry->index_debut - $retour;
                    $montant = $qte * $entry->prix_unitaire;

                    $totalQuantite += $qte;
                    $totalMontant += $montant;
                @endphp
                <tr>
                    <td>{{ $entry->pump->name }}</td>
                    <td>{{ $entry->pump->tank->product->name ?? '-' }}</td>
                    <td>{{ $entry->user->name ?? '-' }}</td>
                    <td>{{ number_format($entry->index_debut, 2, ',', ' ') }}</td>
                    <td>{{ number_format($entry->index_fin, 2, ',', ' ') }}</td>
                    <td>{{ number_format($retour, 2, ',', ' ') }}</td>
                    <td>{{ number_format($qte, 2, ',', ' ') }}</td>
                    <td>{{ number_format($entry->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($montant, 0, ',', ' ') }} F</td>
                    <td>
                        <a href="{{ route('fuel-indexes.edit', $entry->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                            ✏️
                        </a>
                    </td>
                </tr>
            @endforeach

            <tr class="fw-bold table-light">
                <td colspan="6" class="text-end">Total</td>
                <td>{{ number_format($totalQuantite, 2, ',', ' ') }}</td>
                <td></td>
                <td>{{ number_format($totalMontant, 0, ',', ' ') }} F</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</x-app-layout>
