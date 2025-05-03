<x-app-layout>
    <x-slot name="title">Modifier la facture</x-slot>

    <form method="POST" action="{{ route('purchase-invoices.update', $purchaseInvoice) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $purchaseInvoice->date }}" required>
        </div>

        <div class="mb-3">
            <label>Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="">-- Choisir une rotation --</option>
                <option value="6-14" {{ $purchaseInvoice->rotation == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                <option value="14-22" {{ $purchaseInvoice->rotation == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                <option value="22-6" {{ $purchaseInvoice->rotation == '22-6' ? 'selected' : '' }}>22h - 6h</option>
            </select>
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
            <input type="number" step="0.01" name="amount_ht" id="amount_ht" class="form-control" value="{{ $purchaseInvoice->amount_ht }}" required>
        </div>

        <div class="mb-3">
            <label>Valeur TTC</label>
            <input type="number" step="0.01" name="amount_ttc" id="amount_ttc" class="form-control" value="{{ $purchaseInvoice->amount_ttc }}" readonly>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const htInput = document.getElementById('amount_ht');
            const ttcInput = document.getElementById('amount_ttc');

            function updateTTC() {
                const ht = parseFloat(htInput.value) || 0;
                const ttc = ht + (ht * 0.18);
                ttcInput.value = ttc.toFixed(2);
            }

            htInput.addEventListener('input', updateTTC);
            updateTTC();
        });
    </script>
</x-app-layout>
