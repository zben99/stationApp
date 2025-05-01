<x-app-layout>
    <x-slot name="header">Remboursements de {{ $client->name }}</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('credit-topups.show', $client->id) }}" class="btn btn-secondary">← Retour à la fiche client</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Montant</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->date }}</td>
                    <td class="text-success">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $payment->notes ?? '-' }}</td>
                    <td>
                        <a href="{{ route('credit-payments.edit', $payment->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                        <form action="{{ route('credit-payments.destroy', $payment->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Supprimer ce remboursement ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $payments->links('pagination::bootstrap-5') }}
</x-app-layout>
