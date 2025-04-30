<x-app-layout>
    <x-slot name="header">Historique des Approvisionnements (Lubrifiant / PEA / GAZ / Lampes)</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="pull-right">
                <a class="btn btn-success mb-2" href="{{ route('lubricant-receptions.batch.create') }}">
                    <i class="fa fa-plus"></i> Nouvelle réception
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <form method="GET" action="{{ route('lubricant-receptions.batch.index') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Filtrer par catégorie</label>
                            <select name="category" id="category" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Toutes les catégories --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Date</th>
                            <th>Fournisseur</th>
                            <th>Produits reçus</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $key => $batch)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($batch->date_reception)->format('d/m/Y') }}</td>
                                <td>{{ $batch->supplier->name ?? '-' }}</td>
                                <td>
                                    <ul class="mb-0">
                                        @foreach($batch->receptions as $rec)
                                            <li>{{ $rec->product->name }} - {{ $rec->packaging->packaging->label ?? '?' }} : {{ $rec->quantite }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-info" href="{{ route('lubricant-receptions.batch.show', $batch->id) }}">
                                        <i class="bi bi-eye"></i> Détails
                                    </a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('lubricant-receptions.batch.edit', $batch->id) }}">
                                        <i class="bi bi-pencil-square"></i> Modifier
                                    </a>
                                    <form method="POST" action="{{ route('lubricant-receptions.batch.destroy', $batch->id) }}" style="display:inline" id="deleteForm{{ $batch->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $batch->id }})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $batches->links('pagination::bootstrap-5') !!}
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
