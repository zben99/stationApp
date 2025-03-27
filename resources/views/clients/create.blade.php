<x-app-layout>
    <x-slot name="header">Nouveau Client</x-slot>
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
    <form method="POST" action="{{ route('clients.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Crédit</label>
                <input type="number" step="0.01" name="credit_balance" class="form-control" value="0">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Adresse</label>
                <textarea name="address" class="form-control"></textarea>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control"></textarea>
            </div>

            <div class="col-md-6 mb-3">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                    <label class="form-check-label">Actif</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</x-app-layout>
