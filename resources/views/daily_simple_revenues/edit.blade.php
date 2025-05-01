
<x-app-layout>
    <x-slot name="header">Modifier Recette</x-slot>

    <form method="POST" action="{{ route('daily-simple-revenues.update', $dailySimpleRevenue) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $dailySimpleRevenue->date }}" required>
        </div>

        <div class="mb-3">
            <label>Rotation</label>
            <select name="rotation" class="form-control" required>
                <option value="6-14" {{ $dailySimpleRevenue->rotation == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                <option value="14-22" {{ $dailySimpleRevenue->rotation == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                <option value="22-6" {{ $dailySimpleRevenue->rotation == '22-6' ? 'selected' : '' }}>22h - 6h</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="boutique" {{ $dailySimpleRevenue->type == 'boutique' ? 'selected' : '' }}>Boutique</option>
                <option value="lavage" {{ $dailySimpleRevenue->type == 'lavage' ? 'selected' : '' }}>Lavage</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Montant (FCFA)</label>
            <input type="number" name="amount" class="form-control" step="0.01" value="{{ $dailySimpleRevenue->amount }}" required>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
    </form>
</x-app-layout>
