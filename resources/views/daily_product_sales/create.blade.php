<x-app-layout>
    <x-slot name="header">Nouvelle recette journalière (Produits)</x-slot>

    <form method="POST" action="{{ route('daily-product-sales.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="">-- Choisir --</option>
                    <option value="6-14">6h - 14h</option>
                    <option value="14-22">14h - 22h</option>
                    <option value="22-6">22h - 6h</option>
                </select>
            </div>

        </div>


        <hr>

        <div id="product-lines">
            <div class="row align-items-end mb-2 product-line">
                <div class="col-md-4">
                    <label>Produit</label>
                    <select name="sales[0][product_packaging_id]" class="form-control product-select" required>
                        <option value="">-- Choisir --</option>
                        @foreach($products as $pp)
                            <option value="{{ $pp->id }}" data-label="{{ $pp->product->name }} - {{ $pp->packaging->label }}" data-price="{{ $pp->price }}">
                                {{ $pp->product->name }} - {{ $pp->packaging->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Qté</label>
                    <input type="number" step="0.01" name="sales[0][quantity]" class="form-control quantity" required>
                </div>
                <div class="col-md-2">
                    <label>PU</label>
                    <input type="number" step="0.01" name="sales[0][unit_price]" class="form-control unit-price" required>
                </div>
                <div class="col-md-2">
                    <label>Total</label>
                    <input type="text" class="form-control total-line" readonly>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-line mt-4">Supprimer</button>
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
            line.querySelector('.total-line').value = total;
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.total-line').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grand-total').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
        }

        function updateAvailableOptions() {
            const selectedIds = Array.from(document.querySelectorAll('.product-select'))
                .map(sel => sel.value)
                .filter(val => val !== '');

            document.querySelectorAll('.product-select').forEach(select => {
                const current = select.value;
                Array.from(select.options).forEach(opt => {
                    if (opt.value && opt.value !== current) {
                        opt.disabled = selectedIds.includes(opt.value);
                    }
                });
            });
        }

        document.getElementById('add-line').addEventListener('click', function () {
            const original = document.querySelector('.product-line');
            const clone = original.cloneNode(true);

            clone.querySelectorAll('input, select').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${lineIndex}]`);
                if (!el.classList.contains('total-line')) el.value = '';
                if (el.classList.contains('total-line')) el.value = '0.00';
            });

            document.getElementById('product-lines').appendChild(clone);
            lineIndex++;
            updateAvailableOptions();
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('product-select')) {
                const price = e.target.selectedOptions[0].dataset.price;
                const line = e.target.closest('.product-line');
                line.querySelector('.unit-price').value = price || '';
                updateTotal(line);
                updateAvailableOptions();
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
                    updateAvailableOptions();
                    updateGrandTotal();
                }
            }
        });
    </script>
</x-app-layout>
