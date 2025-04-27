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
                    @foreach($product->productPackagings as $productPackaging)
                        <tr>
                            <td>{{ $productPackaging->packaging->label }}</td>
                            <td>{{ $productPackaging->packaging->unit }}</td>
                            <td>{{ number_format($productPackaging->price, 0, ',', ' ') }} F</td>
                            <td>{{ $productPackaging->quantite_disponible }}</td>
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
