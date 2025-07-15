<x-app-layout>
    <x-slot name="title">Paiement de la facture</x-slot>

    <h2>Payer la facture #{{ $invoice->invoice_number }}</h2>


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

<form method="POST" action="{{ route('payments.store', $invoice->id) }}">
    @csrf

    <div class="form-group">
        <label for="amount">Montant du paiement</label>
        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
    </div>

    <div class="form-group">
        <label for="payment_date">Date du paiement</label>
        <input type="date" name="payment_date" id="payment_date" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="rotation">Rotation</label>
        <select name="rotation" id="rotation" class="form-control" required>
            <option value="6-14">6h - 14h</option>
            <option value="14-22">14h - 22h</option>
            <option value="22-6">22h - 6h</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
</form>


    <a href="{{ route('invoices.showPayments', $invoice->id) }}" class="btn btn-secondary mt-3">Retour à la facture</a>
</x-app-layout>
