<x-app-layout>

    <x-slot name="header">Gestion des Conditionnements</x-slot>

    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <form method="GET" class="d-flex gap-2">
                <select name="type" class="form-control form-select-sm">
                    <option value="">Tous les types</option>
                    <option value="lubrifiant" {{ request('type') == 'lubrifiant' ? 'selected' : '' }}>Lubrifiants</option>
                    <option value="gaz" {{ request('type') == 'gaz' ? 'selected' : '' }}>Gaz</option>
                    <option value="lavage" {{ request('type') == 'lavage' ? 'selected' : '' }}>Lavage</option>
                    <option value="pea" {{ request('type') == 'pea' ? 'selected' : '' }}>PEA</option>
                </select>
                <button class="btn btn-sm btn-primary">Filtrer</button>
            </form>

            <a href="{{ route('packagings.create') }}" class="btn btn-primary btn-sm">+ Nouveau conditionnement</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Label</th>
                            <th>Quantité</th>
                            <th>Unité</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packagings as $packaging)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $packaging->label }}</td>
                                <td>{{ $packaging->quantity }}</td>
                                <td>{{ $packaging->unit }}</td>
                                <td>{{ ucfirst($packaging->type ?? '-') }}</td>
                                <td>
                                    <a class="btn btn-sm btn-warning" href="{{ route('packagings.edit', $packaging) }}">
                                        <i class="bi bi-pencil-square"></i> Modifier
                                    </a>
                                    <form method="POST" action="{{ route('packagings.destroy', $packaging) }}"
                                        style="display:inline" id="deleteForm{{ $packaging->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $packaging->id }})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Si tu veux pagination standard Laravel --}}
                {{-- {!! $packagings->links('pagination::bootstrap-5') !!} --}}
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
        $(document).ready(function () {
            $('#dataTable').DataTable({
                paging: false,
                info: false,
                language: {
                    sEmptyTable: "Aucune donnée disponible",
                    sSearch: "Rechercher:",
                    sZeroRecords: "Aucun résultat trouvé",
                    oPaginate: {
                        sFirst: "Premier",
                        sLast: "Dernier",
                        sNext: "Suivant",
                        sPrevious: "Précédent"
                    }
                }
            });
        });
    </script>

</x-app-layout>
