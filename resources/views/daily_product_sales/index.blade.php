<x-app-layout>
    <x-slot name="header">Recettes journalières par produit</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('daily-product-sales.create') }}" class="btn btn-success">
            + Nouvelle saisie
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Produits saisis</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $key => $entries)
                @php [$date, $rotation] = explode('|', $key); @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                    <td>{{ $rotation }}</td>
                    <td>{{ $entries->count() }}</td>
                    <td>
                        <a href="{{ route('daily-product-sales.show', [$date, $rotation]) }}" class="btn btn-sm btn-primary">
                            Voir détails
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
