<x-app-layout>
    <x-slot name="header">Factures d'achat</x-slot>

    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession

    <form method="GET" action="{{ route('purchase-invoices.index') }}" class="row mb-4">
        <div class="col-md-3">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="Date début">
        </div>
        <div class="col-md-3">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="Date fin">
        </div>
        <div class="col-md-3">
            <input type="text" name="supplier" value="{{ request('supplier') }}" class="form-control" placeholder="Fournisseur">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary">Filtrer</button>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Réinitialiser</a>
        </div>
    </form>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('purchase-invoices.export.pdf', request()->query()) }}" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('purchase-invoices.export.excel', request()->query()) }}" class="btn btn-outline-success">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('purchase-invoices.create') }}" class="btn btn-success ms-auto">
            <i class="fa fa-plus"></i> Nouvelle facture
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Numéro</th>
                            <th>Fournisseur</th>
                            <th>Valeur HT</th>
                            <th>Valeur TTC</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $invoice->date }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->supplier_name }}</td>
                                <td>{{ number_format($invoice->amount_ht, 0, ',', ' ') }} F</td>
                                <td>{{ number_format($invoice->amount_ttc, 0, ',', ' ') }} F</td>
                                <td>
                                    <a href="{{ route('purchase-invoices.edit', $invoice) }}" class="btn btn-sm btn-warning">
                                        Modifier
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Aucune facture trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $invoices->links('pagination::bootstrap-5') }}
</x-app-layout>
