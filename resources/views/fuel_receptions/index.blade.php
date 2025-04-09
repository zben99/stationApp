<x-app-layout>
    <x-slot name="title">Réceptions de Carburant</x-slot>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('fuel-receptions.create') }}" class="btn btn-primary mb-3">+ Nouvelle Réception</a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="receptionsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cuve</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th>Fournisseur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receptions as $reception)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reception->tank->code ?? '-' }}</td>
                                <td>{{ $reception->tank->product->name ?? '-' }}</td>
                                <td>{{ $reception->quantite_livree }} L</td>
                                <td>{{ $reception->date_reception->format('d/m/Y') }}</td>
                                <td>{{ $reception->supplier->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('fuel-receptions.edit', $reception) }}" class="btn btn-sm btn-warning">Modifier</a>
                                    <form action="{{ route('fuel-receptions.destroy', $reception) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Confirmer la suppression ?')">
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
                $('#receptionsTable').DataTable();
            });
        </script>
    @endpush
</x-app-layout>
