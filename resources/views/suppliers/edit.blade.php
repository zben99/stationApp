<x-app-layout>
    <x-slot name="header">Modifier le Fournisseur</x-slot>

    <div class="card shadow mb-4">
        <div class="card-body">

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
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nom *</label>
                    <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Adresse</label>
                    <textarea name="address" class="form-control">{{ $supplier->address }}</textarea>
                </div>

                <input type="hidden" name="station_id" value="{{ $supplier->station_id }}">

                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</x-app-layout>
