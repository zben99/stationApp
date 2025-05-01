<x-app-layout>
    <x-slot name="header">Liste des Pompes</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('pumps.create') }}" class="btn btn-success">+ Nouvelle pompe</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nom</th>
                <th>Produit</th>
                <th>Cuve associée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pumps as $i => $pump)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pump->name }}</td>
                    <td>{{ $pump->tank->product->name }}</td>
                    <td>{{ $pump->tank->code ?? '-' }}</td>
                    <td>
                        <a href="{{ route('pumps.edit', $pump->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                        <form action="{{ route('pumps.destroy', $pump->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
