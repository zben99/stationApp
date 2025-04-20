<x-app-layout>
    <x-slot name="header">
        Recharges de solde
    </x-slot>

    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="pull-right">
                <a class="btn btn-success mb-2" href="{{ route('balance-topups.create') }}">
                    <i class="fa fa-plus"></i> Nouvelle recharge
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topups as $key => $topup)
                            <tr>
                                <td></td>
                                <td>{{ $topup->client->name }}</td>
                                <td>{{ number_format($topup->amount, 0, ',', ' ') }} F CFA</td>
                                <td>{{ $topup->date }}</td>
                                <td>{{ $topup->notes }}</td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('balance-topups.edit', $topup->id) }}">
                                        <i class="bi bi-pencil-square"></i> Modifier
                                    </a>

                                    <form method="POST" action="{{ route('balance-topups.destroy', $topup->id) }}" style="display:inline" id="deleteForm{{ $topup->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $topup->id }})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $topups->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
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
