<x-app-layout>
    <x-slot name="header">Modifier relevé - {{ $fuelIndex->pump->name }}</x-slot>

    <form method="POST" action="{{ route('fuel-indexes.update', $fuelIndex) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Index début</label>
            <input type="number" step="0.01" name="index_debut" class="form-control"
                value="{{ old('index_debut', $fuelIndex->index_debut) }}" required>
        </div>

        <div class="mb-3">
            <label>Index fin</label>
            <input type="number" step="0.01" name="index_fin" class="form-control"
                value="{{ old('index_fin', $fuelIndex->index_fin) }}" required>
        </div>

        <div class="mb-3">
            <label>Retour en cuve (litres)</label>
            <input type="number" step="0.01" name="retour_en_cuve" class="form-control"
                value="{{ old('retour_en_cuve', $fuelIndex->retour_en_cuve) }}">
        </div>

        <button class="btn btn-primary">💾 Enregistrer</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
