<x-app-layout>
    <x-slot name="title">Modifier recharge de solde</x-slot>

    <form action="{{ route('balance-topups.update', $balanceTopup) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label for="client_id">Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $client->id == $balanceTopup->client_id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="amount">Montant (F CFA)</label>
            <input type="number" name="amount" value="{{ $balanceTopup->amount }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" name="date" value="{{ $balanceTopup->date }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control">{{ $balanceTopup->notes }}</textarea>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('balance-topups.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
