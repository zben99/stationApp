
<x-app-layout>
    <x-slot name="header">Nouvel avoir servi</x-slot>


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
            <label for="rotation">Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="6-14" {{ old('rotation', $balanceUsage->rotation ?? '') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                <option value="14-22" {{ old('rotation', $balanceUsage->rotation ?? '') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                <option value="22-6" {{ old('rotation', $balanceUsage->rotation ?? '') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('balances.summary') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
