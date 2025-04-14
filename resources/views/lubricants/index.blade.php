<x-app-layout>
    <x-slot name="header">Réceptions de Lubrifiants</x-slot>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('lubricant-receptions.create') }}" class="btn btn-primary mb-3">+ Nouvelle Réception</a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="lubricantTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Fournisseur</th>
                            <th>Prix Vente</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receptions as $reception)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reception->date_reception->format('d/m/Y') }}</td>
                                <td>{{ $reception->product->name ?? '-' }}</td>
                                <td>{{ $reception->quantite }} L</td>
                                <td>{{ $reception->supplier->name ?? '-' }}</td>
                                <td>{{ number_format($reception->prix_vente, 0, ',', ' ') }} F CFA</td>
                                <td>
                                    <a href="{{ route('lubricant-receptions.edit', $reception) }}" class="btn btn-sm btn-warning">Modifier</a>
                                    <form action="{{ route('lubricant-receptions.destroy', $reception) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
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
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#lubricantTable').DataTable();
            });
        </script>
    @endpush
</x-app-layout>
