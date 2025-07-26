<x-app-layout>
    <div class="container">
        <h4 class="mb-4">Nouvelle Réception - Lubrifiant / PEA / GAZ / Lampes / Divers</h4>

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
                <div class="col-md-6">
                    <label for="date_reception" class="form-label">Date de réception</label>
                    <input type="date" name="date_reception" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="rotation">Rotation</label>
                    <select name="rotation" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="6-14" {{ old('rotation', $batch->rotation ?? '') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                        <option value="14-22" {{ old('rotation', $batch->rotation ?? '') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                        <option value="22-6" {{ old('rotation', $batch->rotation ?? '') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label">Fournisseur</label>
                    <select id="supplierSelect" name="supplier_id" class="form-control select2-tag" required>
                        <option value="">-- Sélectionner ou taper --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="num_bc" class="form-label">N° Bon de Commande (BC)</label>
                    <input type="text" name="num_bc" class="form-control" value="{{ old('num_bc', $batch->num_bc ?? '') }}">
                </div>
            </div>

            <!-- Tableau des produits -->
            <table class="table table-bordered" id="products-table">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Produit</th>
                        <th>Conditionnement</th>
                        <th>Quantité</th>
                        <th>Observations</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="products[0][category_id]" class="form-control select-category" required>
                                <option value="">-- Sélectionner catégorie --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="products[0][station_product_id]" class="form-control select-product" required>
                                <option value="">-- Choisir produit --</option>
                            </select>
                        </td>
                        <td>
                            <select name="products[0][product_packaging_id]" class="form-control select-packaging" required>
                                <option value="">-- Sélectionner un format --</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="products[0][quantite]" class="form-control" required></td>
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

        // Filtrer les produits par catégorie




    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('select-category')) {
            const categoryId = e.target.value;
            const productSelect = e.target.closest('tr').querySelector('.select-product');

            // Utilisation de la fonction route() de Laravel pour générer l'URL de la route
            const url = "{{ route('lubricant-receptions.getProductsByCategory', ':categoryId') }}".replace(':categoryId', categoryId);

            // Recharger les produits en fonction de la catégorie sélectionnée
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">-- Choisir produit --</option>';
                    data.forEach(product => {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                    productSelect.innerHTML = options;
                });
        }

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            $('.select2-tag').select2({
                theme: 'bootstrap-5',
                tags: true,
                placeholder: '-- Sélectionner ou taper --',
                width: '100%',
                language: {
                    noResults: () => 'Aucun résultat',
                    inputTooShort: () => 'Tape au moins 1 caractère'
                }
            });
        });
    </script>
</x-app-layout>
