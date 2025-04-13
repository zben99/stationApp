<x-app-layout>

    <x-slot name="header">Réceptions de Carburant</x-slot>



    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession

    <div class="card">
        <div class="card-body">
            <a href="{{ route('fuel-receptions.create') }}" class="btn btn-primary mb-3">+ Nouvelle Réception</a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tanksTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cuve</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th>Fournisseur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($receptions as $reception)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $reception->tank->code ?? '-' }}</td>
                            <td>{{ $reception->tank->product->name ?? '-' }}</td>
                            <td>{{ $reception->quantite_livree }} L</td>
                            <td>{{ $reception->date_reception->format('d/m/Y') }}</td>
                            <td>{{ $reception->supplier->name ?? '-' }}</td>
                            <td>


                                <a class="btn btn-primary btn-sm" href="{{ route('fuel-receptions.edit', $reception) }}">
                                    <i class="bi bi-pencil-square"></i> Modifier
                                </a>


                                <form method="POST" action="{{ route('fuel-receptions.destroy', $reception) }}" style="display:inline" id="deleteForm{{ $reception->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $reception->id }})">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
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



