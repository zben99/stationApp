

<x-app-layout>
    <x-slot name="header">Associer un conditionnement au produit - {{ $product->name }}</x-slot>

    <div class="card">
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
            <form method="POST" action="{{ route('product-packagings.store', $product->id) }}">
                @csrf
                <input type="hidden" name="station_product_id" value="{{ $product->id }}">
                <!-- Sélection du conditionnement -->
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

                <!-- Prix d'achat -->
                <div class="mb-3">
                    <label for="prix_achat" class="form-label">Prix d'achat</label>
                    <input type="number" name="prix_achat" step="0.01" class="form-control"
                           value="{{ old('prix_achat') }}" required>
                </div>

                <!-- Prix de vente -->
                <div class="mb-3">
                    <label for="price" class="form-label">Prix de vente</label>
                    <input type="number" name="price" step="0.01" class="form-control"
                           value="{{ old('price') }}" required>
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock (en unités)</label>
                    <input type="number" name="stock" class="form-control"
                           value="{{ old('stock', 0) }}" required>
                </div>

                <div class="text-end">
                    <button class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

