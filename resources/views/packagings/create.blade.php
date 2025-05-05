<x-app-layout>
    <x-slot name="title">Nouveau conditionnement</x-slot>

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
    <form action="{{ route('packagings.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="label">Label</label>
            <input type="text" name="label" class="form-control" placeholder="Ex : 1L, 12kg, Bidon, etc." required>
        </div>

        <div class="mb-3">
            <label for="quantity">Quantité</label>
            <input type="number" name="quantity" step="0.01" class="form-control" placeholder="Ex : 1, 5, 12.5" required>
        </div>

        <div class="mb-3">
            <label for="unit">Unité</label>
            <select name="unit" class="form-control" required>
                <option value="L">Litres (L)</option>
                <option value="kg">Kilogrammes (kg)</option>
                <option value="u">Unité</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="type">Type</label>
            <select name="type" class="form-control">
                <option value="">-- Facultatif --</option>
                <option value="lubrifiant">Lubrifiant</option>
                <option value="gaz">Gaz</option>
                <option value="lavage">Lavage</option>
                <option value="pea">PEA</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('packagings.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
