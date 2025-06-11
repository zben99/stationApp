<x-app-layout>
    <x-slot name="header">Valider une rotation</x-slot>

    {{-- ==================== ALERTES ERREUR ==================== --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops !</strong> Il y a eu quelques problèmes avec votre saisie.
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form id="validationForm" method="POST" action="{{ route('daily-revenue-validations.store') }}">
        @csrf

        {{-- ==================== DATE & ROTATION ==================== --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control"
                       value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Rotation</label>
                <select name="rotation" id="rotation" class="form-control" required>
                    <option value="">-- Choisir une rotation --</option>
                    <option value="6-14"  {{ old('rotation') == '6-14'  ? 'selected' : '' }}>6h - 14h</option>
                    <option value="14-22" {{ old('rotation') == '14-22' ? 'selected' : '' }}>14h - 22h</option>
                    <option value="22-6"  {{ old('rotation') == '22-6'  ? 'selected' : '' }}>22h - 6h</option>
                </select>
            </div>
        </div>

        {{-- ==================== I. ENCAISSEMENTS ==================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">I. Encaissements</div>
            <div class="card-body row g-3">

                @php
                    $incomeFields = [
                        'fuel_super_amount'  => 'Carburant Super',
                        'fuel_gazoil_amount' => 'Carburant Gazoil',
                        'lub_amount'         => 'Lubrifiants',
                        'pea_amount'         => 'PEA',
                        'gaz_amount'         => 'GAZ',
                        'lampes_amount'      => 'Lampes',
                        'lavage_amount'      => 'Lavage',
                        'boutique_amount'    => 'Boutique',
                        'credit_repaid'      => 'Remboursement crédit',
                        'balance_received'   => 'Avoir perçu',
                    ];
                @endphp

                @foreach ($incomeFields as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" class="form-control"
                               name="{{ $name }}" id="{{ $name }}" value="{{ old($name, 0) }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==================== II. DÉCAISSEMENTS ==================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">II. Décaissements</div>
            <div class="card-body row g-3">
                @foreach ([
                    'expenses'        => 'Dépenses',
                    'credit_received' => 'Crédit accordé',
                    'balance_used'    => 'Avoir servi'
                ] as $name => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" class="form-control"
                               name="{{ $name }}" id="{{ $name }}" value="{{ old($name, 0) }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==================== III. MOUVEMENTS ÉLECTRONIQUES ==================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">III. Mouvements électroniques</div>
            <div class="card-body row g-3">
                @foreach ([
                    'tpe_amount' => 'Vente TPE',
                    'om_amount'  => 'Vente OM',
                    'om_recharge_amount'  => 'Recharge OM',
                    'tpe_recharge_amount' => 'Recharge TPE'

                ] as $name => $label)
                    <div class="col-md-3">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" step="0.01" class="form-control"
                               name="{{ $name }}" id="{{ $name }}" value="{{ old($name, 0) }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==================== IV. RÉCAPITULATIF ==================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">IV. Récapitulatif</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Montant à verser</label>
                    <input type="number" step="0.01" class="form-control"
                           name="cash_amount" id="cash_amount" value="{{ old('cash_amount', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Net à verser</label>
                    <input type="number" step="0.01" class="form-control"
                           name="net_to_deposit" id="net_to_deposit" readonly value="{{ old('net_to_deposit', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Écart de caisse</label>
                    <input type="text" id="cash_gap" class="form-control fw-bold" readonly>
                </div>
            </div>
        </div>

        {{-- ==================== ACTIONS ==================== --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('daily-revenue-validations.index') }}" class="btn btn-secondary">← Annuler</a>
            <button type="submit" class="btn btn-primary">✅ Valider la rotation</button>
        </div>
    </form>

    {{-- ==================== JS ==================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /* ---------- Helpers ---------- */
        const incomeIds  = [
            'fuel_super_amount','fuel_gazoil_amount',
            'lub_amount','pea_amount','gaz_amount','lampes_amount',
            'lavage_amount','boutique_amount',
            'credit_repaid','balance_received','tpe_recharge_amount','om_recharge_amount'
        ];
        const expenseIds = [
            'expenses','credit_received','balance_used',
            'tpe_amount','om_amount'
        ];

        const byId = id => document.getElementById(id);
        const val  = id => parseFloat(byId(id)?.value || 0);

        /* ---------- Calcul net / écart ---------- */
        function calculate() {
            const totalIn  = incomeIds.reduce((sum, id)  => sum + val(id), 0);
            const totalOut = expenseIds.reduce((sum, id) => sum + val(id), 0);
            const net      = (totalIn - totalOut).toFixed(2);

            byId('net_to_deposit').value = net;

            const cash = val('cash_amount');
            const gap  = (cash - net).toFixed(2);

            const gapField = byId('cash_gap');
            gapField.value = (gap > 0 ? '+' : '') + gap + ' FCFA';
            gapField.className = 'form-control fw-bold ' +
                                 (gap > 0 ? 'text-success'
                                          : gap < 0 ? 'text-danger'
                                                    : 'text-muted');

            /* on stocke l’écart sur le form pour la confirmation SweetAlert */
            document.getElementById('validationForm').dataset.gap = gap;
        }

        /* ---------- Fetch auto ---------- */
        async function fetchData() {
            const date     = byId('date').value;
            const rotation = byId('rotation').value;
            if (!date || !rotation) return;

            try {
                const url = `{{ route('daily-revenue-validations.fetch') }}?date=${date}&rotation=${rotation}`;
                const resp = await fetch(url, { headers: { 'Accept':'application/json' } });

                const data = await resp.json();

                if (!resp.ok) throw new Error(data.message || 'Erreur inconnue');

                if (data.message) {
                    Swal.fire('Erreur', data.message, 'error');
                    return;
                }

                /* injection des champs */
                [...incomeIds, ...expenseIds].forEach(id => {
                    if (data[id] !== undefined) byId(id).value = data[id];
                });

                calculate();

            } catch (err) {
                Swal.fire('Erreur', err.message, 'error');
            }
        }

        /* ---------- Événements ---------- */
        document.addEventListener('DOMContentLoaded', () => {
            /* recalc à chaque saisie */
            [...incomeIds, ...expenseIds, 'cash_amount'].forEach(id =>
                byId(id).addEventListener('input', calculate)
            );

            /* auto-fetch quand la date / rotation change */
            ['date','rotation'].forEach(id =>
                byId(id).addEventListener('change', fetchData)
            );

            /* confirmation si écart ≠ 0 FCFA */
            document.getElementById('validationForm').addEventListener('submit', e => {
                const gap = parseFloat(e.target.dataset.gap || 0);
                if (Math.abs(gap) >= 1) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Écart de caisse',
                        text: `Il y a un écart de ${gap} FCFA. Continuer ?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Oui',
                        cancelButtonText: 'Annuler'
                    }).then(result => {
                        if (result.isConfirmed) e.target.submit();
                    });
                }
            });

            calculate();      // calcul initial
        });
    </script>
</x-app-layout>
