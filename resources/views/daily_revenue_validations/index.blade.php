<x-app-layout>
    <x-slot name="header">📋 Historique des validations</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex justify-content-end">
        <a href="{{ route('daily-revenue-validations.create') }}" class="btn btn-success">
            ➕ Nouvelle validation
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Date / Rotation</th>
                        <th>Encaissement</th>
                        <th>Décaissement</th>
                        <th>Mouv. TPE / OM</th>
                        <th class="text-primary">💰 Net à verser</th>
                        <th>Validé par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($validations as $val)
                        @php
                            $encaissement = $val->fuel_amount + $val->product_amount + $val->shop_amount + $val->credit_repaid + $val->balance_received;
                            $decaissement = $val->expenses + $val->credit_received + $val->balance_used;
                            $electronic = $val->tpe_amount + $val->om_amount;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $val->formatted_date }}</strong><br>
                                <span class="badge bg-dark">{{ $val->rotation_label }}</span>
                            </td>
                            <td>{{ number_format($encaissement, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($decaissement, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($electronic, 0, ',', ' ') }} F</td>
                            <td class="fw-bold text-primary">
                                {{ number_format($val->net_to_deposit, 0, ',', ' ') }} F
                            </td>
                            <td>
                                <small>{{ $val->validator->name ?? '—' }}</small><br>
                                <small class="text-muted">{{ $val->validated_at?->format('d/m H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('daily-revenue-validations.show', $val) }}" class="btn btn-sm btn-outline-primary">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Aucune validation enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $validations->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</x-app-layout>
