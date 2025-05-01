<x-app-layout>
    <x-slot name="header">Détails recette {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} - Rotation {{ $rotation }}</x-slot>

    <a href="{{ route('daily-product-sales.index') }}" class="btn btn-secondary mb-3">← Retour</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Conditionnement</th>
                <th>Qté</th>
                <th>PU</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td>  {{ $entry->productPackaging->product->name ?? '-' }}</td>
                    <td>{{ $entry->productPackaging->packaging->label ?? '-' }}</td>



                    <td>{{ $entry->quantity }}</td>
                    <td>{{ number_format($entry->unit_price, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($entry->total_price, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
