<x-app-layout>
    <x-slot name="header">Avoirs perçus de {{ $client->name }}</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('clients.balance', $client->id) }}" class="btn btn-secondary">
            ← Retour à la fiche client
        </a>

        <a href="{{ route('balance-topups.create', ['client_id' => $client->id]) }}" class="btn btn-success">
            <i class="fa fa-plus"></i> Nouvelle recharge
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Rotation</th>
                <th>Montant</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topups as $topup)
                <tr>
                    <td>{{ $topup->date }}</td>
                    <td>{{ $topup->rotation ?? '—' }}</td>
                    <td class="text-primary fw-bold">{{ number_format($topup->amount, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $topup->notes ?? '-' }}</td>
                    <td>
                        <a href="{{ route('balance-topups.edit', $topup->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                        <form action="{{ route('balance-topups.destroy', $topup->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Supprimer cet avoir ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $topups->links('pagination::bootstrap-5') }}
</x-app-layout>
