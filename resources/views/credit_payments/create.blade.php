<x-app-layout>
    <x-slot name="title">Nouveau remboursement</x-slot>

    <form action="{{ route('credit-payments.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="client_id">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="credit_topup_id">Crédit concerné</label>
            <select name="credit_topup_id" class="form-control" required>
                @foreach($creditTopups as $topup)
                    <option value="{{ $topup->id }}">
                        {{ $topup->client->name }} | {{ $topup->date }} | {{ number_format($topup->amount, 0, ',', ' ') }} F
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="amount">Montant</label>
            <input type="number" name="amount" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('credit-payments.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
