
<x-app-layout>
    <x-slot name="header">Avoirs servis</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Montant</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usages as $usage)
                <tr>
                    <td>{{ $usage->date }}</td>
                    <td>{{ $usage->client->name }}</td>
                    <td class="text-success fw-bold">{{ number_format($usage->amount, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $usage->notes ?? '-' }}</td>
                    <td>
                        <a href="{{ route('balance-usages.edit', $usage->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                        <form method="POST" action="{{ route('balance-usages.destroy', $usage->id) }}" style="display:inline-block" onsubmit="return confirm('Supprimer cet avoir ?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $usages->links('pagination::bootstrap-5') }}
</x-app-layout>
