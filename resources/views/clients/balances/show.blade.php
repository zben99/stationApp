<x-app-layout>
    <x-slot name="header">
        Fiche avoirs : {{ $client->name }}

        <br>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('clients.balance.topups', $client->id) }}" class="btn btn-outline-danger btn-sm me-2">
                Voir Avoirs perçus
            </a>
            <a href="{{ route('clients.balance.usages', $client->id) }}" class="btn btn-outline-success btn-sm">
                Voir Avoirs servis
            </a>
        </div>
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('balances.summary') }}" class="btn btn-secondary">
            ← Retour à la liste des clients
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div><strong>📞 Téléphone :</strong> {{ $client->phone }}</div>
            <div><strong>📧 Email :</strong> {{ $client->email ?? '—' }}</div>
            <div><strong>🏢 Station :</strong> {{ $client->station->name ?? '—' }}</div>
            <div>
                <strong>💳 Solde avoir :</strong>
                <span class="badge bg-{{ $client->balanceTopups->sum('amount') > $client->balanceUsages->sum('amount') ? 'warning' : 'success' }}">
                    {{ number_format($client->balanceTopups->sum('amount') - $client->balanceUsages->sum('amount'), 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light text-primary fw-bold">Avoirs perçus</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Rotation</th>
                                <th>Montant</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->balanceTopups as $topup)
                                <tr>
                                    <td>{{ $topup->date }}</td>
                                    <td>{{ $topup->rotation ?? '—' }}</td>
                                    <td class="text-primary fw-bold">{{ number_format($topup->amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $topup->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun avoir perçu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light text-success fw-bold">Avoirs servis</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Rotation</th>
                                <th>Montant</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->balanceUsages as $usage)
                                <tr>
                                    <td>{{ $usage->date }}</td>
                                    <td>{{ $usage->rotation ?? '—' }}</td>
                                    <td class="text-success fw-bold">{{ number_format($usage->amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $usage->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun avoir servi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
