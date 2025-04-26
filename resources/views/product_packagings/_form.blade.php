@csrf

@if(isset($availablePackagings))
<div class="mb-3">
    <label for="packaging_id" class="form-label">Conditionnement</label>
    <select name="packaging_id" class="form-control" required>
        <option value="">-- Sélectionner --</option>
        @foreach($availablePackagings as $packaging)
            <option value="{{ $packaging->id }}" {{ old('packaging_id') == $packaging->id ? 'selected' : '' }}>
                {{ $packaging->label }} ({{ $packaging->unit }} )
            </option>
        @endforeach
    </select>
</div>
@endif

<div class="mb-3">
    <label for="price" class="form-label">Prix de vente</label>
    <input type="number" name="price" step="0.01" class="form-control"
           value="{{ old('price', $productPackaging->pivot->price ?? $productPackaging->price ?? '') }}">
</div>

<div class="mb-3">
    <label for="stock" class="form-label">Stock (en unités)</label>
    <input type="number" name="stock" class="form-control"
           value="{{ old('stock', $productPackaging->pivot->stock ?? $productPackaging->stock ?? 0) }}">
</div>

<div class="text-end">
    <button class="btn btn-success">
        <i class="fas fa-save me-1"></i> Enregistrer
    </button>
</div>
