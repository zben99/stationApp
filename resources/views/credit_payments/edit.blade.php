<x-app-layout>
    <x-slot name="title">Modifier remboursement</x-slot>

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
            <label for="rotation">Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="6-14" {{ old('rotation', $creditPayment->rotation ?? '') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                <option value="14-22" {{ old('rotation', $creditPayment->rotation ?? '') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                <option value="22-6" {{ old('rotation', $creditPayment->rotation ?? '') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control">{{ $creditPayment->notes }}</textarea>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('credit-payments.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
