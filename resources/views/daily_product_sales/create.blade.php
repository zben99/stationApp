<x-app-layout>
    <x-slot name="header">Nouvelle recette journalière (Lubrifiant / PEA / GAZ / Lampes)</x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('daily-product-sales.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label>Rotation</label>
                <select name="rotation" class="form-control @error('rotation') is-invalid @enderror" required>
                    <option value="">-- Choisir --</option>
                    <option value="6-14" {{ old('rotation') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ old('rotation') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6" {{ old('rotation') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                </select>
                @error('rotation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr>

        <div id="product-lines">
            <div class="row align-items-end mb-2 product-line">
                <div class="col-md-2">
                    <label>Catégorie</label>
                    <select class="form-control category-select">
                        <option value="">-- Choisir --</option>
                        @foreach($productCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Produit</label>
                    <select name="sales[0][product_packaging_id]" class="form-control product-select" required>
                        <option value="">-- Choisir --</option>
                        @foreach($products as $pp)
                            <option value="{{ $pp->id }}"
                                data-category="{{ $pp->product->category_id }}"
                                data-price="{{ $pp->price }}">
                                {{ $pp->product->name }} - {{ $pp->packaging->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label>Qté</label>
                    <input type="number" step="0.01" name="sales[0][quantity]" class="form-control quantity" required>
                </div>
                <div class="col-md-2">
                    <label>PU</label>
                    <input type="number" step="0.01" name="sales[0][unit_price]" class="form-control unit-price" required readonly>
                </div>
                <div class="col-md-2">
                    <label>Total</label>
                    <input type="text" class="form-control total-display" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-line mt-4">✕</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-line" class="btn btn-outline-primary btn-sm">+ Ajouter un produit</button>

        <div class="mt-4">
            <h5>Total global : <span id="grand-total">0 FCFA</span></h5>
        </div>

        <br>
        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>

    <script>
        let lineIndex = 1;

        function updateTotal(line) {
            const qty = parseFloat(line.querySelector('.quantity').value) || 0;
            const pu = parseFloat(line.querySelector('.unit-price').value) || 0;
            const total = (qty * pu).toFixed(2);
            line.querySelector('.total-display').value = total + ' FCFA';

            line.querySelector('.total-line')?.remove();
            line.insertAdjacentHTML('beforeend', `<input type="hidden" class="total-line" value="${total}">`);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.total-line').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grand-total').textContent = new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(total) + ' FCFA';
        }

        document.getElementById('add-line').addEventListener('click', function () {
            const original = document.querySelector('.product-line');
            const clone = original.cloneNode(true);
            clone.querySelectorAll('input, select').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/g, `[${lineIndex}]`);
                if (!el.classList.contains('total-line')) el.value = '';
                if (el.classList.contains('unit-price')) el.readOnly = true;
                if (el.classList.contains('total-display')) el.value = '';
            });

            clone.querySelector('.product-select').innerHTML = original.querySelector('.product-select').innerHTML;

            // Cacher les produits au départ
            clone.querySelectorAll('.product-select option').forEach(opt => {
                if (opt.value) opt.hidden = true;
            });

            document.getElementById('product-lines').appendChild(clone);
            lineIndex++;
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.product-select').forEach(select => {
                Array.from(select.options).forEach(opt => {
                    if (opt.value) opt.hidden = true;
                });
            });
        });

        document.addEventListener('change', function (e) {
            // Catégorie sélectionnée → filtrer produits
            if (e.target.classList.contains('category-select')) {
                const categoryId = e.target.value;
                const productSelect = e.target.closest('.product-line').querySelector('.product-select');

                Array.from(productSelect.options).forEach(opt => {
                    if (!opt.value) {
                        opt.hidden = false;
                    } else if (!categoryId) {
                        opt.hidden = true;
                    } else {
                        opt.hidden = opt.dataset.category !== categoryId;
                    }
                });

                productSelect.selectedIndex = 0;
                productSelect.dispatchEvent(new Event('change'));
            }

            // Produit sélectionné → charger prix
            if (e.target.classList.contains('product-select')) {
                const price = e.target.selectedOptions[0]?.dataset.price || 0;
                const line = e.target.closest('.product-line');
                line.querySelector('.unit-price').value = price;
                updateTotal(line);
            }
        });

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
                const line = e.target.closest('.product-line');
                updateTotal(line);
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-line')) {
                const lines = document.querySelectorAll('.product-line');
                if (lines.length > 1) {
                    e.target.closest('.product-line').remove();
                    updateGrandTotal();
                }
            }
        });
    </script>
</x-app-layout>
