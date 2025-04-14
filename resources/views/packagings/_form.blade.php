@csrf

<div class="mb-3">
    <label for="label" class="form-label">Nom du conditionnement</label>
    <input type="text" name="label" class="form-control" value="{{ old('label', $packaging->label ?? '') }}" required placeholder="Ex: 1L, 5L, Fût">
</div>

<div class="mb-3">
    <label for="volume_litre" class="form-label">Volume (en litres)</label>
    <input type="number" step="0.01" name="volume_litre" class="form-control" value="{{ old('volume_litre', $packaging->volume_litre ?? '') }}" required>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save me-1"></i> Enregistrer
    </button>
</div>
