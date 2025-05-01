<x-app-layout>
    <x-slot name="header">Relevé journalier par pompe</x-slot>

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
    <form method="POST" action="{{ route('fuel-indexes.store') }}">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="date">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label for="rotation">Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="6-14">6h - 14h</option>
                    <option value="14-22">14h - 22h</option>
                    <option value="22-6">22h - 6h</option>
                </select>
            </div>
        </div>

        <hr>

        @foreach($pumps as $i => $pump)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold">
                    {{ $pump->name }} ({{ $pump->tank->product->name ?? 'Produit inconnu' }})
                </div>
                <div class="card-body">
                    <input type="hidden" name="pumps[{{ $i }}][pump_id]" value="{{ $pump->id }}">

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Index début</label>
                            <input type="number" step="0.01" name="pumps[{{ $i }}][index_debut]" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Index fin</label>
                            <input type="number" step="0.01" name="pumps[{{ $i }}][index_fin]" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Prix unitaire (FCFA)</label>
                            <input type="number" step="0.01" name="pumps[{{ $i }}][prix_unitaire]" class="form-control"
                                   value="{{ $pump->tank->product->price ?? '' }}" required readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Montant déclaré (FCFA)</label>
                            <input type="number" step="0.01" name="pumps[{{ $i }}][montant_declare]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <button class="btn btn-primary">Enregistrer les relevés</button>
    </form>
</x-app-layout>
