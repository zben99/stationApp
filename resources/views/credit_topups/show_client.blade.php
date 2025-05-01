<x-app-layout>
    <x-slot name="header">
        <div class="align-items-center flex-wrap">
            <h5 class="mb-0">Fiche client : {{ $client->name }}</h5>
            <br>
            <div class="mt-2 mt-md-0">
                <a href="{{ route('clients.topups', $client->id) }}" class="btn btn-outline-danger btn-sm me-2">
                    Voir crédits
                </a>
                <a href="{{ route('clients.payments', $client->id) }}" class="btn btn-outline-success btn-sm">
                    Voir remboursements
                </a>
            </div>
        </div>
    </x-slot>
    <div class="mb-3">
        <a href="{{ route('credit-topups.index') }}" class="btn btn-secondary">
            ← Retour à la liste des clients
        </a>
    </div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div><strong>📞 Téléphone :</strong> {{ $client->phone }}</div>
            <div><strong>📧 Email :</strong> {{ $client->email ?? '—' }}</div>

            <div>
                <strong>💰 Solde crédit :</strong>
                <span class="badge bg-{{ $client->credit_balance > 0 ? 'warning' : 'success' }}">
                    {{ number_format($client->credit_balance, 2, ',', ' ') }} FCFA
                </span>
            </div>
            <div>
                <strong>📊 Statut :</strong>
                <span class="badge bg-{{ $client->credit_status_color }}">
                    {{ $client->credit_status }}
                </span>
            </div>
        </div>
    </div>


    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light text-danger fw-bold">Crédits reçus</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->creditTopups as $topup)
                                <tr>
                                    <td>{{ $topup->date }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($topup->amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $topup->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucun crédit enregistré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light text-success fw-bold">Remboursements effectués</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->creditPayments as $payment)
                                <tr>
                                    <td>{{ $payment->date }}</td>
                                    <td class="text-success fw-bold">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucun remboursement enregistré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
