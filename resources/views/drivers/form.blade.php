@csrf

<div class="mb-3">
    <label for="name">Nom</label>
    <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label for="phone">Téléphone</label>
    <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label for="permis">N° de permis</label>
    <input type="text" name="permis" value="{{ old('permis', $driver->permis ?? '') }}" class="form-control">
</div>

<button type="submit" class="btn btn-success">Enregistrer</button>
<a href="{{ route('drivers.index') }}" class="btn btn-secondary">Annuler</a>
