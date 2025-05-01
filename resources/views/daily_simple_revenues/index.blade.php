<x-app-layout>
    <x-slot name="header">Recettes Lavage & Boutique</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <a href="{{ route('daily-simple-revenues.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Nouvelle recette
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Rotation</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($revenues as $rev)
                        <tr>
                            <td>{{ $rev->date }}</td>
                            <td>{{ $rev->rotation }}</td>
                            <td>{{ ucfirst($rev->type) }}</td>
                            <td>{{ number_format($rev->amount, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <a href="{{ route('daily-simple-revenues.edit', $rev) }}" class="btn btn-sm btn-primary">Modifier</a>
                                <form method="POST" action="{{ route('daily-simple-revenues.destroy', $rev) }}" style="display:inline-block">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $revenues->links('pagination::bootstrap-5') }}
        </div>
    </div>
</x-app-layout>
