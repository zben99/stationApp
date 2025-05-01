<x-app-layout>
    <x-slot name="header">Avoirs servis de {{ $client->name }}</x-slot>

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
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usages as $key => $usage)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="text-success fw-bold">{{ number_format($usage->amount, 0, ',', ' ') }} F CFA</td>
                                <td>{{ $usage->date }}</td>
                                <td>{{ $usage->notes ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('balance-usages.edit', $usage->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                                    <form action="{{ route('balance-usages.destroy', $usage->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Supprimer cet avoir servi ?')">
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
