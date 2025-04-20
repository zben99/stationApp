<x-app-layout>

    <x-slot name="header">
        Remboursements de crédit
    </x-slot>

    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="pull-right">
                <a class="btn btn-success mb-2" href="{{ route('credit-payments.create') }}">
                    <i class="fa fa-plus"></i> Nouveau remboursement
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
                            <th>Crédit lié</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $key => $payment)
                            <tr>
                                <td></td>
                                <td>{{ $payment->client->name }}</td>
                                <td>
                                    Crédit du {{ $payment->creditTopup->date ?? '-' }}
                                </td>
                                <td>{{ number_format($payment->amount, 0, ',', ' ') }} F CFA</td>
                                <td>{{ $payment->date }}</td>
                                <td>{{ $payment->notes }}</td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('credit-payments.edit', $payment->id) }}">
                                        <i class="bi bi-pencil-square"></i> Modifier
                                    </a>

                                    <form method="POST" action="{{ route('credit-payments.destroy', $payment->id) }}" style="display:inline" id="deleteForm{{ $payment->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $payment->id }})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $payments->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>

    {{-- SweetAlert for delete confirmation --}}
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

    {{-- DataTables init --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                paging: false,
                info: false,
                language: {
                    sEmptyTable: "Aucune donnée disponible dans le tableau",
                    sInfo: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    sInfoEmpty: "Affichage de 0 à 0 sur 0 entrées",
                    sInfoFiltered: "(filtré à partir de _MAX_ entrées)",
                    sLengthMenu: "Afficher _MENU_ entrées",
                    sLoadingRecords: "Chargement...",
                    sProcessing: "Traitement...",
                    sSearch: "Rechercher:",
                    sZeroRecords: "Aucun résultat trouvé",
                    oPaginate: {
                        sFirst: "Premier",
                        sLast: "Dernier",
                        sNext: "Suivant",
                        sPrevious: "Précédent"
                    },
                    oAria: {
                        sSortAscending: ": activer pour trier la colonne par ordre croissant",
                        sSortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                }
            });
        });
    </script>

</x-app-layout>
