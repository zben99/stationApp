<x-app-layout>
<div class="container">
    <h4 class="mb-4">Nouvelle Réception - Produits multiples</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lubricant-receptions.batch.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="date_reception" class="form-label">Date de réception</label>
                <input type="date" name="date_reception" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="supplier_id" class="form-label">Fournisseur</label>
                <select name="supplier_id" class="form-control">
                    <option value="">-- Sélectionner --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <table class="table table-bordered" id="products-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Conditionnement</th>
                    <th>Quantité</th>
                    <th>Prix achat</th>
                    <th>Observations</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="products[0][station_product_id]" class="form-control select-product" required>
                            <option value="">-- Choisir produit --</option>
                            @foreach($stationProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="products[0][product_packaging_id]" class="form-control select-packaging" required>
                            <option value="">-- Sélectionner un format --</option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="products[0][quantite]" class="form-control" required></td>
                    <td><input type="number" step="0.01" name="products[0][prix_achat]" class="form-control"></td>
                    <td><input type="text" name="products[0][observations]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="add-row" class="btn btn-secondary mb-3">+ Ajouter un produit</button>
        <button type="submit" class="btn btn-primary">Enregistrer la réception</button>
    </form>
</div>

<script>
    let rowIndex = 1;
    document.getElementById('add-row').addEventListener('click', function () {
        const tbody = document.querySelector('#products-table tbody');
        const newRow = tbody.rows[0].cloneNode(true);

        [...newRow.querySelectorAll('input, select')].forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const updatedName = name.replace(/\[0\]/, `[${rowIndex}]`);
                input.setAttribute('name', updatedName);
                input.value = '';
            }
        });

        tbody.appendChild(newRow);
        rowIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('tr');
            const tbody = document.querySelector('#products-table tbody');
            if (tbody.rows.length > 1) row.remove();
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('select-product')) {
            const productSelect = e.target;
            const packagingSelect = productSelect.closest('tr').querySelector('.select-packaging');
            const productId = productSelect.value;

            packagingSelect.innerHTML = '<option value="">Chargement...</option>';

            fetch(`/lubricant-receptions/packagings/${productId}`)
                .then(response => response.json())
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
