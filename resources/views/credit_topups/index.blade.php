<x-app-layout>
    <x-slot name="header">
        Recharges de crédit
    </x-slot>

    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="pull-right">
                <a class="btn btn-success mb-2" href="{{ route('credit-topups.create') }}">
                    <i class="fa fa-plus"></i> Nouvelle recharge
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Montant crédit</th>
                            <th>Total remboursé</th>
                            <th>Restant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topups as $key => $topup)
                            <tr>
                                <td></td>
                                <td>{{ $topup->client->name }}</td>
                                <td>{{ number_format($topup->amount, 0, ',', ' ') }} F</td>
                                <td>{{ number_format($topup->total_payments, 0, ',', ' ') }} F</td>
                                <td>{{ number_format($topup->remaining_balance, 0, ',', ' ') }} F</td>
                                <td>
                                    @php
                                        $status = $topup->status;
                                    @endphp
                                    <span class="badge
                                        {{ $status == 'Totalement remboursé' ? 'bg-success' : ($status == 'Partiellement remboursé' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>{{ $topup->date }}</td>
                                <td>
                                    <a href="{{ route('credit-topups.edit', $topup->id) }}" class="btn btn-sm btn-primary">
                                        Modifier
                                    </a>
                                    <a href="{{ route('credit-topups.show', $topup->id) }}" class="btn btn-sm btn-info">
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $topups->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</x-app-layout>
