
<x-app-layout>
    <x-slot name="header">Modifier avoir servi</x-slot>

    <form method="POST" action="{{ route('balance-usages.update', $balanceUsage->id) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Client</label>
            <select name="client_id" class="form-control" required>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $balanceUsage->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Montant</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $balanceUsage->amount }}" required>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $balanceUsage->date }}" required>
        </div>

        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ $balanceUsage->notes }}</textarea>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('balance-usages.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
