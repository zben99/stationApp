<x-app-layout>
    <x-slot name="title">Nouvelle facture d'achat</x-slot>

    <form method="POST" action="{{ route('purchase-invoices.store') }}">
        @csrf

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Numéro de la facture</label>
            <input type="text" name="invoice_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Nom du fournisseur</label>
            <input type="text" name="supplier_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Valeur HT</label>
            <input type="number" step="0.01" name="amount_ht" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Valeur TTC</label>
            <input type="number" step="0.01" name="amount_ttc" class="form-control" required>
        </div>

        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
