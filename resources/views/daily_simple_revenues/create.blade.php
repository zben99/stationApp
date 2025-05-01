
<x-app-layout>
    <x-slot name="header">Nouvelle Recette</x-slot>

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

    <form method="POST" action="{{ route('daily-simple-revenues.store') }}">
        @csrf

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="6-14">6h - 14h</option>
                <option value="14-22">14h - 22h</option>
                <option value="22-6">22h - 6h</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Montant Boutique (FCFA)</label>
            <input type="number" name="boutique" class="form-control" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label>Montant Lavage (FCFA)</label>
            <input type="number" name="lavage" class="form-control" step="0.01" min="0" required>
        </div>

        <button class="btn btn-success">Enregistrer les recettes</button>
    </form>

</x-app-layout>
