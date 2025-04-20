<x-app-layout>
    <x-slot name="title">Modifier la facture</x-slot>

    <form method="POST" action="{{ route('purchase-invoices.update', $purchaseInvoice) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $purchaseInvoice->date }}" required>
        </div>

        <div class="mb-3">
            <label>Numéro de la facture</label>
            <input type="text" name="invoice_number" class="form-control" value="{{ $purchaseInvoice->invoice_number }}" required>
        </div>

        <div class="mb-3">
            <label>Nom du fournisseur</label>
            <input type="text" name="supplier_name" class="form-control" value="{{ $purchaseInvoice->supplier_name }}" required>
        </div>

        <div class="mb-3">
            <label>Valeur HT</label>
            <input type="number" step="0.01" name="amount_ht" class="form-control" value="{{ $purchaseInvoice->amount_ht }}" required>
        </div>

        <div class="mb-3">
            <label>Valeur TTC</label>
            <input type="number" step="0.01" name="amount_ttc" class="form-control" value="{{ $purchaseInvoice->amount_ttc }}" required>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
