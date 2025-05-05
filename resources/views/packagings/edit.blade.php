<x-app-layout>
    <x-slot name="title">Modifier le conditionnement</x-slot>

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
    <form action="{{ route('packagings.update', $packaging) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label for="label">Label</label>
            <input type="text" name="label" value="{{ $packaging->label }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="quantity">Quantité</label>
            <input type="number" name="quantity" step="0.01" value="{{ $packaging->quantity }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="unit">Unité</label>
            <select name="unit" class="form-control" required>
                <option value="L" {{ $packaging->unit == 'L' ? 'selected' : '' }}>Litres (L)</option>
                <option value="kg" {{ $packaging->unit == 'kg' ? 'selected' : '' }}>Kilogrammes (kg)</option>
                <option value="u" {{ $packaging->unit == 'u' ? 'selected' : '' }}>Unité</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="type">Type</label>
            <select name="type" class="form-control">
                <option value="">-- Facultatif --</option>
                <option value="lubrifiant" {{ $packaging->type == 'lubrifiant' ? 'selected' : '' }}>Lubrifiant</option>
                <option value="gaz" {{ $packaging->type == 'gaz' ? 'selected' : '' }}>Gaz</option>
                <option value="lavage" {{ $packaging->type == 'lavage' ? 'selected' : '' }}>Lavage</option>
                <option value="pea" {{ $packaging->type == 'pea' ? 'selected' : '' }}>PEA</option>
                <option value="autre" {{ $packaging->type == 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('packagings.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
