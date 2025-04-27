<x-app-layout>
    <x-slot name="title">Modifier la Réception - Lubrifiant</x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('lubricant-receptions.update', $lubricantReception->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="station_product_id">Produit</label>
                        <select name="station_product_id" id="station_product_id" class="form-control @error('station_product_id') is-invalid @enderror">
                            <option value="">Sélectionnez un produit</option>
                            @foreach($stationProducts as $product)
                                <option value="{{ $product->id }}"
                                    {{ (old('station_product_id', $lubricantReception->station_product_id) == $product->id) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('station_product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="product_packaging_id">Packaging</label>
                        <select name="product_packaging_id" id="product_packaging_id" class="form-control">
                            <option value="">-- Choisir un packaging --</option>
                            @foreach ($packagings as $packaging)
                                <option value="{{ $packaging['id'] }}"
                                    {{ old('product_packaging_id', $lubricantReception->product_packaging_id) == $packaging['id'] ? 'selected' : '' }}>
                                    {{ $packaging['name'] }} ({{ $packaging['unit'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_packaging_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="supplier_id">Fournisseur</label>
                        <select name="supplier_id" id="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror">
                            <option value="">Sélectionnez un fournisseur (facultatif)</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ (old('supplier_id', $lubricantReception->supplier_id) == $supplier->id) ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="date_reception">Date de réception</label>
                        <input type="date" name="date_reception" class="form-control" value="{{ old('date_reception', $lubricantReception->date_reception ? $lubricantReception->date_reception->format('Y-m-d') : '') }}">

                        @error('date_reception')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="quantite">Quantité reçue</label>
                        <input type="number" name="quantite" id="quantite" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite', $lubricantReception->quantite) }}" step="0.01">
                        @error('quantite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="prix_achat">Prix d'achat</label>
                        <input type="number" name="prix_achat" id="prix_achat" class="form-control @error('prix_achat') is-invalid @enderror" value="{{ old('prix_achat', $lubricantReception->prix_achat) }}" step="0.01">
                        @error('prix_achat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="prix_vente">Prix de vente</label>
                        <input type="number" name="prix_vente" id="prix_vente" class="form-control @error('prix_vente') is-invalid @enderror" value="{{ old('prix_vente', $lubricantReception->prix_vente) }}" step="0.01">
                        @error('prix_vente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="observations">Observations</label>
                        <textarea name="observations" id="observations" class="form-control @error('observations') is-invalid @enderror" rows="1">{{ old('observations', $lubricantReception->observations) }}</textarea>
                        @error('observations')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Mettre à jour la réception</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('station_product_id').addEventListener('change', function() {
            const productId = this.value;
            const packagingSelect = document.getElementById('product_packaging_id');
            packagingSelect.innerHTML = '<option value="">Chargement...</option>';

            if(productId) {
                fetch(`/product/${productId}/packagings`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Sélectionnez un packaging</option>';
                        data.forEach(function(packaging) {
                            options += `<option value="${packaging.id}">${packaging.name} (${packaging.unit})</option>`;
                        });
                        packagingSelect.innerHTML = options;
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        packagingSelect.innerHTML = '<option value="">Erreur lors du chargement</option>';
                    });
            } else {
                packagingSelect.innerHTML = '<option value="">Sélectionnez un produit d\'abord</option>';
            }
        });
    </script>
</x-app-layout>
