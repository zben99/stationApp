<x-app-layout>
    <x-slot name="header">Modifier Client</x-slot>

    <form method="POST" action="{{ route('clients.update', $client->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="name" class="form-control" value="{{ $client->name }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="phone" class="form-control" value="{{ $client->phone }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $client->email }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Crédit</label>
                <input type="number" step="0.01" name="credit_balance" class="form-control" value="{{ $client->credit_balance }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Adresse</label>
                <textarea name="address" class="form-control">{{ $client->address }}</textarea>
            </div>



            <div class="col-md-6 mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control">{{ $client->notes }}</textarea>
            </div>

            <div class="col-md-6 mb-3">
                {{-- champ caché pour gérer le cas décoché --}}
                <input type="hidden" name="is_active" value="0">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ $client->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">Actif</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</x-app-layout>
