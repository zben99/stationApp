<x-app-layout>
    <x-slot name="header">Suivi des Dépenses</x-slot>

    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary mb-3">+ Nouvelle dépense</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Rotation</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Montant</th>
                            <th>Pièce Jointe</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                                <td>{{ $expense->rotation ?? '—' }}</td>
                                <td>{{ $expense->category->name ?? '-' }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ number_format($expense->montant, 0, ',', ' ') }} F CFA</td>
                                <td>
                                    @if($expense->piece_jointe)
                                        <a href="{{ Storage::url($expense->piece_jointe) }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-paperclip me-1"></i> Voir
                                        </a>
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('expenses.edit', $expense) }}">
                                        <i class="bi bi-pencil-square"></i> Modifier
                                    </a>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" style="display:inline" id="deleteForm{{ $expense->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $expense->id }})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $expenses->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(userId) {
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
                    document.getElementById('deleteForm' + userId).submit();
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
