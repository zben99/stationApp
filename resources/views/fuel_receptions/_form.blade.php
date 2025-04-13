@csrf

<div class="row">
    <div class="col mb-3">
        <label for="tank_id" class="form-label">Cuve</label>
        <select name="tank_id" id="tank_id" class="form-control" required>
            <option value="">-- Sélectionner --</option>
            @foreach($tanks as $tank)
                <option value="{{ $tank->id }}" {{ old('tank_id', $fuelReception->tank_id ?? '') == $tank->id ? 'selected' : '' }}>
                    {{ $tank->code }} ({{ $tank->product->name ?? '-' }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col mb-3">
        <label for="date_reception" class="form-label">Date de Réception</label>
        <input type="date" name="date_reception" id="date_reception" class="form-control" value="{{ old('date_reception', isset($fuelReception) ? $fuelReception->date_reception->format('Y-m-d') : '') }}" required>
    </div>
</div>


<div class="row">
    <div class="col mb-3">
        <label for="quantite_livree" class="form-label">Quantité Livrée (L)</label>
        <input type="number" name="quantite_livree" class="form-control" step="0.01" value="{{ old('quantite_livree', $fuelReception->quantite_livree ?? '') }}" required>
    </div>

    <div class="col mb-3">
        <label for="densite" class="form-label">Densité (optionnel)</label>
        <input type="number" name="densite" class="form-control" step="0.001" value="{{ old('densite', $fuelReception->densite ?? '') }}">
    </div>

</div>

<div class="row">

    <div class="col mb-3">
        <label for="supplier_id" class="form-label">Fournisseur</label>
        <select name="supplier_id" class="form-control">
            <option value="">-- Sélectionner --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $fuelReception->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col mb-3">
        <label for="num_bl" class="form-label">N° BL</label>
        <input type="text" name="num_bl" class="form-control" value="{{ old('num_bl', $fuelReception->num_bl ?? '') }}">
    </div>
</div>


<div class="mb-3">
    <label for="remarques" class="form-label">Remarques</label>
    <textarea name="remarques" class="form-control" rows="3">{{ old('remarques', $fuelReception->remarques ?? '') }}</textarea>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success">Enregistrer</button>
</div>
