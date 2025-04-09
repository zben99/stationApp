@csrf


<div class="mb-3">
    <label for="station_product_id" class="form-label">Produit (Carburant)</label>
    <select name="station_product_id" id="station_product_id" class="form-control" required>
        <option value="">-- Sélectionner --</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" {{ old('station_product_id', $tank->station_product_id ?? '') == $product->id ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="code" class="form-label">Code de la Cuve</label>
    <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $tank->code ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="capacite" class="form-label">Capacité (en Litres)</label>
    <input type="number" step="0.01" name="capacite" id="capacite" class="form-control" value="{{ old('capacite', $tank->capacite ?? '') }}" required>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success">Enregistrer</button>
</div>
