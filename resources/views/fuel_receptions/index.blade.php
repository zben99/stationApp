<x-app-layout>
    <x-slot name="header">Liste des Dépotages</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="pull-right">
                <a class="btn btn-success mb-2" href="{{ route('fuel-receptions.create') }}">
                    <i class="fa fa-plus"></i> Nouveau dépotage
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Date</th>
                            <th>BL</th>
                            <th>Transporteur</th>
                            <th>Chauffeur</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receptions as $key => $reception)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($reception->date_reception)->format('d/m/Y') }}</td>
                                <td>{{ $reception->num_bl ?? '-' }}</td>
                                <td>{{ $reception->transporter->name ?? '-' }}</td>
                                <td>{{ $reception->driver->name ?? '-' }}</td>
                                <td>
                                    <a class="btn btn-sm btn-info" href="{{ route('fuel-receptions.show', $reception->id) }}">
                                        <i class="bi bi-eye"></i> Détails
                                    </a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('fuel-receptions.edit', $reception->id) }}">
                                        <i class="bi bi-pencil-square"></i> Modifier
                                    </a>
                                    <form method="POST" action="{{ route('fuel-receptions.destroy', $reception->id) }}" style="display:inline" id="deleteForm{{ $reception->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $reception->id }})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $receptions->links('pagination::bootstrap-5') !!}
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
                "paging": false,
                "info": false,
                "language": {
                    "sEmptyTable": "Aucune donnée disponible dans le tableau",
                    "sSearch": "Rechercher:",
                    "sZeroRecords": "Aucun résultat trouvé"
                }
            });
        });
    </script>
</x-app-layout>
