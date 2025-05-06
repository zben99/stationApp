<x-app-layout>
    <x-slot name="header">Valider une rotation</x-slot>

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

    <form id="validationForm" method="POST" action="{{ route('daily-revenue-validations.store') }}">
        @csrf

        <div class="row mb-4">
            <div class="col-md-4">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label for="rotation" class="form-label">Rotation</label>
                <select name="rotation" id="rotation" class="form-control" required>
                    <option value="">-- Choisir une rotation --</option>
                    <option value="6-14" {{ old('rotation') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ old('rotation') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6" {{ old('rotation') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                </select>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">I. Encaissements</div>
            <div class="card-body row g-3">
                @foreach ([
                    'fuel_amount' => 'Carburants',
                    'product_amount' => 'Lubrifiants / PEA / Gaz / Lampes',
                    'shop_amount' => 'Boutique / Lavage',
                    'credit_repaid' => 'Remboursement de crédit',
                    'balance_received' => 'Avoir perçu'
                ] as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" name="{{ $name }}" id="{{ $name }}" class="form-control" value="{{ old($name, 0) }}">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">II. Décaissements</div>
            <div class="card-body row g-3">
                @foreach ([
                    'expenses' => 'Dépenses',
                    'credit_received' => 'Crédit accordé',
                    'balance_used' => 'Avoir servi'
                ] as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" name="{{ $name }}" id="{{ $name }}" class="form-control" value="{{ old($name, 0) }}">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">III. Mouvements électroniques</div>
            <div class="card-body row g-3">
                @foreach ([
                    'tpe_amount' => 'Recharge TPE',
                    'om_amount' => 'Recharge OM'
                ] as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" name="{{ $name }}" id="{{ $name }}" class="form-control" value="{{ old($name, 0) }}">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">IV. Récapitulatif</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Montant en caisse</label>
                    <input type="number" step="0.01" name="cash_amount" id="cash_amount" class="form-control" value="{{ old('cash_amount', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Net à verser</label>
                    <input type="number" step="0.01" name="net_to_deposit" id="net_to_deposit" class="form-control" readonly value="{{ old('net_to_deposit', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Écart de caisse</label>
                    <input type="text" id="cash_gap" class="form-control fw-bold" readonly>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('daily-revenue-validations.index') }}" class="btn btn-secondary">
                ← Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                ✅ Valider la rotation
            </button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function getValue(id) {
            return parseFloat(document.getElementById(id)?.value || 0);
        }

        function calculate() {
            const totalIn = getValue('fuel_amount') + getValue('product_amount') + getValue('shop_amount') + getValue('credit_repaid') + getValue('balance_received');
            const totalOut = getValue('expenses') + getValue('credit_received') + getValue('balance_used') + getValue('tpe_amount') + getValue('om_amount');
            const net = (totalIn - totalOut).toFixed(2);
            const cash = getValue('cash_amount');
            const gap = (cash - net).toFixed(2);

            document.getElementById('net_to_deposit').value = net;
            const gapField = document.getElementById('cash_gap');
            gapField.value = (gap > 0 ? '+' : '') + gap + ' FCFA';
            gapField.className = 'form-control fw-bold ' + (gap > 0 ? 'text-success' : gap < 0 ? 'text-danger' : 'text-muted');
            document.getElementById('validationForm').dataset.gap = gap;
        }

        function fetchData() {
            const date = document.getElementById('date').value;
            const rotation = document.getElementById('rotation').value;
            if (date && rotation) {
                fetch(`{{ route('daily-revenue-validations.fetch') }}?date=${date}&rotation=${rotation}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.message) {
                            Swal.fire('Erreur', data.message, 'error');
                            return;
                        }
                        const ids = [
                            'fuel_amount','product_amount','shop_amount',
                            'credit_repaid','balance_received','expenses',
                            'credit_received','balance_used','tpe_amount','om_amount'
                        ];
                        ids.forEach(id => {
                            if (data[id] !== undefined) {
                                document.getElementById(id).value = data[id];
                            }
                        });
                        calculate();
                    })
                    .catch(() => Swal.fire('Erreur', 'Chargement des données impossible.', 'error'));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            [
                'fuel_amount', 'product_amount', 'shop_amount',
                'credit_repaid', 'balance_received',
                'expenses', 'credit_received', 'balance_used',
                'tpe_amount', 'om_amount', 'cash_amount'
            ].forEach(id => {
                document.getElementById(id).addEventListener('input', calculate);
            });

            document.getElementById('rotation').addEventListener('change', fetchData);
            document.getElementById('date').addEventListener('change', fetchData);

            document.getElementById('validationForm').addEventListener('submit', function (e) {
                const gap = parseFloat(e.target.dataset.gap || 0);
                if (Math.abs(gap) >= 1) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Ecart de caisse',
                        text: `Il y a un écart de ${gap} FCFA. Continuer ?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Oui',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                }
            });

            calculate();
        });
    </script>
</x-app-layout>
