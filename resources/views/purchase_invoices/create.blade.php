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
            <input type="number" step="0.01" name="amount_ht" id="amount_ht" class="form-control" required>

        </div>

        <div class="mb-3">
            <label>Valeur TTC</label>
            <input type="number" step="0.01" name="amount_ttc" id="amount_ttc" class="form-control" readonly>
        </div>

        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Annuler</a>
    </form>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const htInput = document.getElementById('amount_ht');
            const ttcInput = document.getElementById('amount_ttc');

            htInput.addEventListener('input', function () {
                const ht = parseFloat(htInput.value) || 0;
                const tva = ht * 0.18;
                const ttc = ht + tva;
                ttcInput.value = ttc.toFixed(2);
            });
        });
    </script>

</x-app-layout>
