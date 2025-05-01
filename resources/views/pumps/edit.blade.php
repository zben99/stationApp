<x-app-layout>
    <x-slot name="header">Modifier la Pompe</x-slot>

    <form method="POST" action="{{ route('pumps.update', $pump) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Nom de la pompe</label>
            <input type="text" name="name" class="form-control" value="{{ $pump->name }}" required>
        </div>



        <div class="mb-3">
            <label>Cuve associée</label>
            <select name="tank_id" class="form-control" required>
                @foreach($tanks as $tank)
                    <option value="{{ $tank->id }}" {{ $pump->tank_id == $tank->id ? 'selected' : '' }}>{{ $tank->code }} ({{ $tank->product->name }})</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Mettre à jour</button>
    </form>
</x-app-layout>
