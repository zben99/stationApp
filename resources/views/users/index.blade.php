<x-app-layout>


    <x-slot name="header">

        Gestion des utilisateurs

    </x-slot>


    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession



                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="pull-right">
                                <a class="btn btn-success mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Créer un nouvel utilisateur</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom complêt</th>
                                            <th>Compte</th>
                                            <th>Rôles</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $key => $user)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                              @if(!empty($user->getRoleNames()))
                                                @foreach($user->getRoleNames() as $v)
                                                   <label class="badge bg-success">{{ $v }}</label>
                                                @endforeach
                                              @endif
                                            </td>
                                            <td>



                                                  @can('user-edit')
                                                        <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}">
                                                            <i class="bi bi-pencil-square"></i> Modifier
                                                        </a>
                                                    @endcan

                                                    @can('user-delete')
                                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline" id="deleteForm{{ $user->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">
                                                                <i class="bi bi-trash"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    @endcan
                                            </td>
                                        </tr>
                                     @endforeach


                                    </tbody>
                                </table>
                                {!! $data->links('pagination::bootstrap-5') !!}
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


