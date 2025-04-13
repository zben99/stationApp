@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="expense_category_id" class="form-label">Catégorie</label>
        <select name="expense_category_id" class="form-control" required>
            <option value="">-- Choisir une catégorie --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="date_depense" class="form-label">Date</label>
        <input type="date" name="date_depense" class="form-control" value="{{ old('date_depense', isset($expense) ? $expense->date_depense->format('Y-m-d') : '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="montant" class="form-label">Montant</label>
        <input type="number" step="0.01" name="montant" class="form-control" value="{{ old('montant', $expense->montant ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="piece_jointe" class="form-label">Justificatif (PDF/Image)</label>
        <input type="file" name="piece_jointe" class="form-control">
        @if (!empty($expense->piece_jointe))
            <small class="d-block mt-1">Fichier actuel : <a href="{{ Storage::url($expense->piece_jointe) }}" target="_blank">Télécharger</a></small>
        @endif
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description ?? '') }}</textarea>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save me-1"></i> Enregistrer
    </button>
</div>
