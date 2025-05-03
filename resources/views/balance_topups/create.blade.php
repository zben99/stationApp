<x-app-layout>
    <x-slot name="title">Nouvelle recharge de solde</x-slot>

    <form action="{{ route('balance-topups.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="client_id">Client</label>
            <select name="client_id" id="client_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="amount">Montant (F CFA)</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="rotation">Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="6-14" {{ old('rotation', $balanceTopup->rotation ?? '') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                <option value="14-22" {{ old('rotation', $balanceTopup->rotation ?? '') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                <option value="22-6" {{ old('rotation', $balanceTopup->rotation ?? '') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="notes">Notes (facultatif)</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('balances.summary') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
