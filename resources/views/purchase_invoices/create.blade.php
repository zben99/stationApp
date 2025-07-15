<x-app-layout>
    <x-slot name="title">Nouvelle facture d'achat</x-slot>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchase-invoices.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="">-- Choisir une rotation --</option>
                    <option value="6-14">6h - 14h</option>
                    <option value="14-22">14h - 22h</option>
                    <option value="22-6">22h - 6h</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Numéro de la facture</label>
                <input type="text" name="invoice_number" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Nom du fournisseur</label>
                <input type="text" name="supplier_name" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Valeur HT</label>
                <input type="number" step="0.01" name="amount_ht" id="amount_ht" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Valeur TTC</label>
                <input type="number" step="0.01" name="amount_ttc" id="amount_ttc" class="form-control" readonly>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
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
