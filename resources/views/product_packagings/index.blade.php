<x-app-layout>
    <x-slot name="header">Conditionnements pour {{ $product->name }}</x-slot>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('product-packagings.create', $product->id) }}" class="btn btn-primary mb-3">+ Associer un conditionnement</a>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Conditionnement</th>
                        <th>Unité</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->packagings as $packaging)
                        <tr>
                            <td>{{ $packaging->label }}</td>
                            <td>{{ $packaging->unit }}</td>
                            <td>{{ number_format($packaging->pivot->price, 0, ',', ' ') }} F</td>
                            <td>{{ $product->lubricantStock?->quantite_actuelle ?? 0 }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning">Modifier</a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
