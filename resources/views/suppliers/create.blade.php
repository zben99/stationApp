<x-app-layout>
    <x-slot name="header">Ajouter un Fournisseur</x-slot>

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
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nom *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Adresse</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>

                <input type="hidden" name="station_id" value="{{ auth()->user()->station_id }}">

                <button type="submit" class="btn btn-success">Enregistrer</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</x-app-layout>
