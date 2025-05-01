<x-app-layout>
    <x-slot name="title">Modifier remboursement</x-slot>

    <form action="{{ route('credit-payments.update', $creditPayment) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label for="client_id">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $client->id == $creditPayment->client_id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label for="amount">Montant</label>
            <input type="number" name="amount" value="{{ $creditPayment->amount }}" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" name="date" value="{{ $creditPayment->date }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control">{{ $creditPayment->notes }}</textarea>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('credit-payments.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
