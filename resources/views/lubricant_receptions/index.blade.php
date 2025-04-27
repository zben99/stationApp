






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
                            <th>Fournisseur</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Achat</th>
                            <th>Prix Vente</th>
                            <th>Date</th>
                            <th>Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receptions as $reception)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reception->supplier->name ?? 'N/A' }}</td>
                                <td> {{ $reception->product->name }} {{ $reception->packaging->packaging->label }}</td>
                                <td>{{ $reception->quantite }} {{ $reception->packaging->unit }}</td>
                                <td>{{ number_format($reception->prix_achat, (intval($reception->prix_acha) == $reception->prix_acha) ? 0 : 2, '.', ' ') }} FCFA</td>
                                <td> {{ number_format($reception->prix_vente, (intval($reception->prix_vente) == $reception->prix_vente) ? 0 : 2, '.', ' ') }} FCFA</td>
                                <td>{{ $reception->date_reception->format('d/m/Y') }}</td>

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
