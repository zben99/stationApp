<x-app-layout>
    <x-slot name="header">Avoirs perçus de {{ $client->name }}</x-slot>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('clients.balance', $client->id) }}" class="btn btn-secondary">
            ← Retour à la fiche client
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Rotation</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topups as $key => $topup)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="text-primary fw-bold">{{ number_format($topup->amount, 0, ',', ' ') }} F CFA</td>
                                <td>{{ $topup->date }}</td>
                                <td>{{ $topup->rotation ?? '—' }}</td>
                                <td>{{ $topup->notes ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('balance-topups.edit', $topup->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                                    <form action="{{ route('balance-topups.destroy', $topup->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Supprimer cet avoir ?')">
                                        @csrf @method('DELETE')
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

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                paging: false,
                info: false,
                language: {
                    sEmptyTable: "Aucune donnée disponible dans le tableau",
                    sSearch: "Rechercher:",
                    sZeroRecords: "Aucun résultat trouvé"
                }
            });
        });
    </script>
</x-app-layout>
