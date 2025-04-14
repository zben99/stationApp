@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="station_product_id" class="form-label">Produit lubrifiant</label>
        <select name="station_product_id" class="form-select" required>
            <option value="">-- Sélectionner --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ old('station_product_id', $lubricantReception->station_product_id ?? '') == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="date_reception" class="form-label">Date de réception</label>
        <input type="date" name="date_reception" class="form-control"
            value="{{ old('date_reception', isset($lubricantReception) ? $lubricantReception->date_reception->format('Y-m-d') : '') }}"
            required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="quantite" class="form-label">Quantité reçue (L)</label>
        <input type="number" step="0.01" name="quantite" class="form-control"
            value="{{ old('quantite', $lubricantReception->quantite ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="supplier_id" class="form-label">Fournisseur</label>
        <select name="supplier_id" class="form-select">
            <option value="">-- Aucun --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $lubricantReception->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="prix_achat" class="form-label">Prix d'achat (par L)</label>
        <input type="number" step="0.01" name="prix_achat" class="form-control"
            value="{{ old('prix_achat', $lubricantReception->prix_achat ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="prix_vente" class="form-label">Prix de vente (par L)</label>
        <input type="number" step="0.01" name="prix_vente" class="form-control"
            value="{{ old('prix_vente', $lubricantReception->prix_vente ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label for="observations" class="form-label">Observations</label>
    <textarea name="observations" class="form-control" rows="3">{{ old('observations', $lubricantReception->observations ?? '') }}</textarea>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save me-1"></i> Enregistrer
    </button>
</div>
