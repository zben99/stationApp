


<x-app-layout>
    <x-slot name="header">
        Valider une rotation
    </x-slot>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('daily-revenue-validations.store') }}">
        @csrf

        <div class="row mb-4">
            <div class="col-md-4">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label for="rotation" class="form-label">Rotation</label>
                <select name="rotation" class="form-control" required>
                    <option value="">-- Choisir une rotation --</option>
                    <option value="6-14" {{ old('rotation') == '6-14' ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ old('rotation') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6" {{ old('rotation') == '22-6' ? 'selected' : '' }}>22h - 6h</option>
                </select>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">Récapitulatif des montants</div>
            <div class="card-body row g-3">
                @php
                    $fields = [
                        'fuel_amount' => 'Carburant',
                        'product_amount' => 'Lubrifiants / PEA / Gaz / Lampes',
                        'shop_amount' => 'Boutique/Lavage',
                        'om_amount' => 'OM',
                        'tpe_amount' => 'TPE',
                        'balance_received' => 'Avoir perçu',
                        'balance_used' => 'Avoir servi',
                        'credit_received' => 'Crédit reçu',
                        'credit_repaid' => 'Remboursement crédit',
                        'expenses' => 'Dépenses',
                        'net_to_deposit' => 'Net à verser'
                    ];
                @endphp

                @foreach($fields as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" name="{{ $name }}" id="{{ $name }}" class="form-control" value="{{ old($name, 0) }}" {{ $name == 'net_to_deposit' ? 'readonly' : '' }}>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('daily-revenue-validations.index') }}" class="btn btn-secondary">
                ← Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                Valider la rotation
            </button>
        </div>
    </form>

    <script>
        function fetchRevenueData() {
            const date = document.querySelector('input[name="date"]').value;
            const rotation = document.querySelector('select[name="rotation"]').value;

            if (date && rotation) {
                fetch(`{{ route('daily-revenue-validations.fetch') }}?date=${date}&rotation=${rotation}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        const fields = [
                            'fuel_amount',
                            'product_amount',
                            'shop_amount',
                            'om_amount',
                            'tpe_amount',
                            'balance_received',
                            'balance_used',
                            'credit_received',
                            'credit_repaid',
                            'expenses'
                        ];

                        fields.forEach(field => {
                            const input = document.getElementById(field);
                            if (input) {
                                input.value = data[field] ?? 0;
                            }
                        });

                        calculateNet();
                    }).catch(async err => {
                            let message = "Erreur de chargement des données.";
                            try {
                                const res = await err.response.json();
                                message = res.message || message;
                            } catch (e) {
                                // fallback pour erreur réseau ou réponse non JSON
                                console.error("Erreur de parsing :", e);
                            }

                            alert(message);
                            console.error(err);
                        });
            }
        }

        function calculateNet() {
            const fields = [
                'fuel_amount',
                'product_amount',
                'shop_amount',
                'om_amount',
                'tpe_amount',
                'balance_received',
                'credit_received'
            ];

            const subtractions = [
                'balance_used',
                'credit_repaid',
                'expenses'
            ];

            const netField = document.getElementById('net_to_deposit');

            let totalIn = fields.reduce((sum, id) => sum + (parseFloat(document.getElementById(id).value) || 0), 0);
            let totalOut = subtractions.reduce((sum, id) => sum + (parseFloat(document.getElementById(id).value) || 0), 0);

            netField.value = (totalIn - totalOut).toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.querySelector('input[name="date"]');
            const rotationSelect = document.querySelector('select[name="rotation"]');

            dateInput.addEventListener('change', fetchRevenueData);
            rotationSelect.addEventListener('change', fetchRevenueData);

            [...document.querySelectorAll('input[type="number"]')].forEach(input => {
                input.addEventListener('input', calculateNet);
            });

            calculateNet();
        });
    </script>
</x-app-layout>
