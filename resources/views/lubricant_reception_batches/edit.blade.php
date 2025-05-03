<x-app-layout>
    <x-slot name="header">Modifier le lot de réception</x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lubricant-receptions.batch.update', $batch->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="date_reception" class="form-label">Date de réception</label>
                <input type="date" name="date_reception" class="form-control" value="{{ $batch->date_reception->format('Y-m-d') }}" required>
            </div>

            <div class="col-md-3 mb-3">
                <label for="rotation">Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="6-14" {{ old('rotation', $batch->rotation ?? '') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ old('rotation', $batch->rotation ?? '') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6" {{ old('rotation', $batch->rotation ?? '') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                </select>
            </div>


            <div class="col-md-3 mb-3">
                <label for="supplier_id" class="form-label">Fournisseur</label>
                <select name="supplier_id" class="form-control">
                    <option value="">-- Sélectionner --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $batch->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>




        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="num_bc" class="form-label">N° Bon de Commande (BC)</label>
                <input type="text" name="num_bc" class="form-control" value="{{  $batch->num_bc}}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="num_bl" class="form-label">N° Bon de Livraison (BL)</label>
                <input type="text" name="num_bl" class="form-control" value="{{ $batch->num_bl }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Produits reçus</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Conditionnement</th>
                        <th>Quantité</th>
                        <th>Prix achat</th>
                        <th>Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->receptions as $i => $rec)
                        <tr>
                            <input type="hidden" name="products[{{ $i }}][id]" value="{{ $rec->id }}">

                            <td>
                                <select name="products[{{ $i }}][station_product_id]" class="form-control select-product" required>
                                    <option value="">-- Produit --</option>
                                    @foreach($stationProducts as $prod)
                                        <option value="{{ $prod->id }}" {{ $rec->station_product_id == $prod->id ? 'selected' : '' }}>
                                            {{ $prod->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <select name="products[{{ $i }}][product_packaging_id]" class="form-control select-packaging" required>
                                    <option value="">-- Format --</option>
                                    @foreach($packagingOptionsByProduct[$rec->station_product_id] ?? [] as $pack)
                                        <option value="{{ $pack['id'] }}" {{ (int)$rec->product_packaging_id === (int)$pack['id'] ? 'selected' : '' }}>
                                            {{ $pack['name'] }} ({{ $pack['unit'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td><input type="number" step="0.01" name="products[{{ $i }}][quantite]" value="{{ $rec->quantite }}" class="form-control" required></td>
                            <td><input type="number" step="0.01" name="products[{{ $i }}][prix_achat]" value="{{ $rec->prix_achat }}" class="form-control"></td>
                            <td><input type="text" name="products[{{ $i }}][observations]" value="{{ $rec->observations }}" class="form-control"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('lubricant-receptions.batch.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('select-product')) {
                const productSelect = e.target;
                const tr = productSelect.closest('tr');
                const packagingSelect = tr.querySelector('.select-packaging');
                const productId = productSelect.value;

                packagingSelect.innerHTML = '<option value="">Chargement...</option>';

                fetch(`/lubricant-receptions/packagings/${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        packagingSelect.innerHTML = '<option value="">-- Choisir format --</option>';
                        data.forEach(p => {
                            packagingSelect.innerHTML += `<option value="${p.id}">${p.name} (${p.unit})</option>`;
                        });
                    });
            }
        });
    </script>
</x-app-layout>
