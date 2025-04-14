<x-app-layout>
    <x-slot name="title">Conditionnements pour {{ $product->name }}</x-slot>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('product-packagings.create', $product->id) }}" class="btn btn-primary mb-3">+ Associer un conditionnement</a>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Conditionnement</th>
                        <th>Volume (L)</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->packagings as $packaging)
                        <tr>
                            <td>{{ $packaging->label }}</td>
                            <td>{{ $packaging->volume_litre }}</td>
                            <td>{{ number_format($packaging->pivot->price, 0, ',', ' ') }} F</td>
                            <td>{{ $packaging->pivot->stock }}</td>
                            <td>
                                <a href="{{ route('product-packagings.edit', $packaging->pivot->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="{{ route('product-packagings.destroy', $packaging->pivot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
