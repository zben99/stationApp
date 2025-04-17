@csrf

<div class="mb-3">
    <label for="name">Nom</label>
    <input type="text" name="name" value="{{ old('name', $transporter->name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label for="phone">Téléphone</label>
    <input type="text" name="phone" value="{{ old('phone', $transporter->phone ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label for="email">Email</label>
    <input type="email" name="email" value="{{ old('email', $transporter->email ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label for="address">Adresse</label>
    <input type="text" name="address" value="{{ old('address', $transporter->address ?? '') }}" class="form-control">
</div>

<button type="submit" class="btn btn-success">Enregistrer</button>
<a href="{{ route('transporters.index') }}" class="btn btn-secondary">Annuler</a>
