<x-app-layout>
    <x-slot name="header">Relevé journalier par pompe</x-slot>

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

    <form method="POST" action="{{ route('fuel-indexes.store') }}">
        @csrf

        <div class="row mb-4">
            <div class="col-12 col-md-4 mb-2">
                <label for="date">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-12 col-md-4 mb-2">
                <label for="rotation">Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="6-14">6h - 14h</option>
                    <option value="14-22">14h - 22h</option>
                    <option value="22-6">22h - 6h</option>
                </select>
            </div>
        </div>

        <hr>

        @php
            $sorted = collect($pumps)->sortBy(function($pump) {
                $name = strtolower($pump->tank->product->name ?? '');
                return str_contains($name, 'super') ? '0_' . strtolower($pump->name) : '1_' . strtolower($pump->name);
            });
            $currentFuel = null;
        @endphp

        @foreach($sorted as $i => $pump)
            @php
                $fuelType = strtoupper($pump->tank->product->name ?? 'INCONNU');
            @endphp

            @if ($fuelType !== $currentFuel)
                <h5 class="mt-4 mb-2 text-primary fw-bold">{{ $fuelType }}</h5>
                @php $currentFuel = $fuelType; @endphp
            @endif

            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body p-3">
                    <h6 class="fw-semibold mb-3">{{ $pump->name }}</h6>

                    <input type="hidden" name="pumps[{{ $i }}][pump_id]" value="{{ $pump->id }}">
                    <input type="hidden" name="pumps[{{ $i }}][prix_unitaire]" value="{{ $pump->tank->product->price ?? '' }}">

                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label>Index début</label>
                            <input type="number"
                                   step="0.01"
                                   name="pumps[{{ $i }}][index_debut]"
                                   class="form-control index-debut"
                                   value="{{ old('pumps.' . $i . '.index_debut', $lastIndexes[$pump->id] ?? '') }}"
                                   required
                                   readonly
                                   ondblclick="this.removeAttribute('readonly')">
                            <small class="text-muted">Double-cliquez pour modifier</small>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
                            <label>Index fin</label>
                            <input type="number" step="0.01" name="pumps[{{ $i }}][index_fin]" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
                            <label>Retour en cuve (litres)</label>
                            <input type="number" step="0.01" name="pumps[{{ $i }}][retour_en_cuve]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-end mt-4">
            <button class="btn btn-primary w-100 w-md-auto">✅ Enregistrer les relevés</button>
        </div>
    </form>
</x-app-layout>
