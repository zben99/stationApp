<x-app-layout>
    <x-slot name="header">
        Recharges de crédit
    </x-slot>

    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="pull-right">
                <a class="btn btn-success mb-2" href="{{ route('credit-topups.create') }}">
                    <i class="fa fa-plus"></i> Nouvelle recharge
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Montant crédit</th>
                            <th>Total remboursé</th>
                            <th>Restant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topups as $key => $topup)
                            <tr>
                                <td></td>
                                <td>{{ $topup->client->name }}</td>
                                <td>{{ number_format($topup->amount, 0, ',', ' ') }} F</td>
                                <td>{{ number_format($topup->total_payments, 0, ',', ' ') }} F</td>
                                <td>{{ number_format($topup->remaining_balance, 0, ',', ' ') }} F</td>
                                <td>
                                    @php
                                        $status = $topup->status;
                                    @endphp
                                    <span class="badge
                                        {{ $status == 'Totalement remboursé' ? 'bg-success' : ($status == 'Partiellement remboursé' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>{{ $topup->date }}</td>
                                <td>
                                    <a href="{{ route('credit-topups.edit', $topup->id) }}" class="btn btn-sm btn-primary">
                                        Modifier
                                    </a>
                                    <a href="{{ route('credit-topups.show', $topup->id) }}" class="btn btn-sm btn-info">
                                        Détails
                                    </a>
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
                    // Soumettre le formulaire si l'utilisateur confirme
                    document.getElementById('deleteForm' + userId).submit();
                }
            });
        }
    </script>

<!-- Inclure jQuery et DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": false,  // Désactive la pagination
            "info": false,    // Désactive l'affichage des informations de pagination
            "language": {
                "sEmptyTable": "Aucune donnée disponible dans le tableau",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                "sInfoEmpty": "Affichage de 0 à 0 sur 0 entrées",
                "sInfoFiltered": "(filtré à partir de _MAX_ entrées)",
                "sLengthMenu": "Afficher _MENU_ entrées",
                "sLoadingRecords": "Chargement...",
                "sProcessing": "Traitement...",
                "sSearch": "Rechercher:",
                "sZeroRecords": "Aucun résultat trouvé",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sLast": "Dernier",
                    "sNext": "Suivant",
                    "sPrevious": "Précédent"
                },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    });
</script>
</x-app-layout>
