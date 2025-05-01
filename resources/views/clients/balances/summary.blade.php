<x-app-layout>
    <x-slot name="header">
        Suivi des Avoirs (Perçu / Servi)
    </x-slot>

    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <a class="btn btn-success mb-2" href="{{ route('balance-topups.create') }}">
                <i class="fa fa-plus"></i> Avoir perçu
            </a>
            <a class="btn btn-success mb-2" href="{{ route('balance-usages.create') }}">
                <i class="fa fa-plus"></i> Avoir servi
            </a>
        </div>


        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Avoir perçu</th>
                            <th>Avoir servi</th>
                            <th>Solde</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $key => $client)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $client->name }}</td>
                                <td>{{ number_format($client->balanceTopups->sum('amount'), 0, ',', ' ') }} F</td>
                                <td>{{ number_format($client->balanceUsages->sum('amount'), 0, ',', ' ') }} F</td>
                                <td>{{ number_format($client->balanceTopups->sum('amount') - $client->balanceUsages->sum('amount'), 0, ',', ' ') }} F</td>
                                <td>
                                    <a href="{{ route('clients.balance', $client->id) }}" class="btn btn-sm btn-info">
                                        Voir détail
                                    </a>
                                    <a href="{{ route('balance-topups.index') }}" class="btn btn-sm btn-danger">
                                        Voir perçus
                                    </a>
                                    <a href="{{ route('balance-usages.index') }}" class="btn btn-sm btn-success">
                                        Voir servis
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Etes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
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
