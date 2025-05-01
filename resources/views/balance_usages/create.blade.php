
<x-app-layout>
    <x-slot name="header">Nouvel avoir servi</x-slot>

    <form method="POST" action="{{ route('balance-usages.store') }}">
        @csrf

        <div class="mb-3">
            <label>Client</label>
            <select name="client_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Montant</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('balance-usages.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
