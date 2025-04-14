<x-app-layout>
    <x-slot name="header">Liste des Conditionnements</x-slot>

    <div class="card">
        <div class="card-body">
            <a href="{{ route('packagings.create') }}" class="btn btn-primary mb-3">+ Nouveau Conditionnement</a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="packagingTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Volume (L)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packagings as $packaging)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $packaging->label }}</td>
                                <td>{{ $packaging->volume_litre }} L</td>
                                <td>
                                    <a href="{{ route('packagings.edit', $packaging) }}" class="btn btn-sm btn-warning">Modifier</a>
                                    <form action="{{ route('packagings.destroy', $packaging) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#packagingTable').DataTable();
            });
        </script>
    @endpush
</x-app-layout>
