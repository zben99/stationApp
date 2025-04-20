<x-app-layout>
    <x-slot name="header">Détail du crédit</x-slot>

    <div class="card mb-4">
        <div class="card-body">
            <h5><strong>Client :</strong> {{ $creditTopup->client->name }}</h5>
            <p><strong>Montant :</strong> {{ number_format($creditTopup->amount, 0, ',', ' ') }} F CFA</p>
            <p><strong>Date :</strong> {{ $creditTopup->date }}</p>
            <p><strong>Statut :</strong> <span class="badge
                {{ $creditTopup->status == 'Totalement remboursé' ? 'bg-success' : ($creditTopup->status == 'Partiellement remboursé' ? 'bg-warning text-dark' : 'bg-danger') }}">
                {{ $creditTopup->status }}
            </span></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Remboursements associés</div>
        <div class="card-body">
            @if ($creditTopup->payments->isEmpty())
                <p>Aucun remboursement enregistré.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditTopup->payments as $payment)
                            <tr>
                                <td>{{ $payment->date }}</td>
                                <td>{{ number_format($payment->amount, 0, ',', ' ') }} F CFA</td>
                                <td>{{ $payment->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
