<x-app-layout>
    <x-slot name="header">Historique des validations de rotation</x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('daily-revenue-validations.create') }}" class="btn btn-success">
            + Nouvelle Validation
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Rotation</th>
                        <th>Carburant</th>
                        <th>Produits</th>
                        <th>Boutique</th>
                        <th>OM</th>
                        <th>TPE</th>
                        <th>Avoir perçu</th>
                        <th>Avoir servi</th>
                        <th>Crédit reçu</th>
                        <th>Remboursement</th>
                        <th>Dépenses</th>
                        <th><strong>Net à verser</strong></th>
                        <th>Validé par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($validations as $val)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($val->date)->format('d/m/Y') }}</td>
                            <td>{{ $val->rotation }}</td>
                            <td>{{ number_format($val->fuel_amount, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->product_amount, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->shop_amount, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->om_amount, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->tpe_amount, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->balance_received, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->balance_used, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->credit_received, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->credit_repaid, 0, ',', ' ') }} F</td>
                            <td>{{ number_format($val->expenses, 0, ',', ' ') }} F</td>
                            <td><strong>{{ number_format($val->net_to_deposit, 0, ',', ' ') }} F</strong></td>
                            <td>{{ $val->user->name ?? '—' }} <br>
                                <small class="text-muted">{{ $val->validated_at ? \Carbon\Carbon::parse($val->validated_at)->format('d/m/Y H:i') : '' }}</small>
                            </td>
                            <td>
                                {{-- Si besoin, ajoute un bouton pour show ou delete --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center">Aucune validation enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $validations->links('pagination::bootstrap-5') }}
        </div>
    </div>
</x-app-layout>
