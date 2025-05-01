<x-app-layout>
    <x-slot name="header">
        Détails relevé du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} (Rotation {{ $rotation }})
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('fuel-indexes.index') }}" class="btn btn-secondary">← Retour à la liste</a>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Pompe</th>
                <th>Produit</th>
                <th>Pompiste</th>
                <th>Index début</th>
                <th>Index fin</th>
                <th>Qté (L)</th>
                <th>PU</th>
                <th>Montant</th>
                <th>Déclaré</th>
                <th>Écart</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td>{{ $entry->pump->name }}</td>
                    <td>{{ $entry->pump->tank->product->name ?? '-' }}</td>
                    <td>{{ $entry->user->name ?? '-' }}</td>
                    <td>{{ $entry->index_debut }}</td>
                    <td>{{ $entry->index_fin }}</td>
                    <td>{{ number_format($entry->quantite_vendue, 2, ',', ' ') }}</td>
                    <td>{{ number_format($entry->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($entry->montant_total, 0, ',', ' ') }} F</td>
                    <td class="text-primary">{{ number_format($entry->montant_declare ?? 0, 0, ',', ' ') }} F</td>
                    <td class="{{ $entry->ecart == 0 ? '' : ($entry->ecart > 0 ? 'text-success' : 'text-danger') }}">
                        {{ number_format($entry->ecart, 0, ',', ' ') }} F
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
