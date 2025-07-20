<x-app-layout>
    <x-slot name="header">
        🧾 Détails de la validation du {{ $validation->formatted_date }} ({{ $validation->rotation_label }})
    </x-slot>

    <div class="mb-3">
        <a href="{{ route('daily-revenue-validations.index') }}" class="btn btn-secondary">
            ← Retour à la liste
        </a>
    </div>

    <div class="row g-4">
        {{-- I. ENCAISSEMENTS --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">I. Encaissements</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Carburant Super :
                            <strong>{{ number_format($validation->fuel_super_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Carburant Gazoil :
                            <strong>{{ number_format($validation->fuel_gazoil_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Lubrifiants :
                            <strong>{{ number_format($validation->lub_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            PEA :
                            <strong>{{ number_format($validation->pea_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            GAZ :
                            <strong>{{ number_format($validation->gaz_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Lampes :
                            <strong>{{ number_format($validation->lampes_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Lavage :
                            <strong>{{ number_format($validation->lavage_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Boutique :
                            <strong>{{ number_format($validation->boutique_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Remboursement crédit :
                            <strong>{{ number_format($validation->credit_repaid, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Avoir perçu :
                            <strong>{{ number_format($validation->balance_received, 0, ',', ' ') }} F</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- II. DÉCAISSEMENTS --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">II. Décaissements</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Crédit accordé :
                            <strong>{{ number_format($validation->credit_received, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Avoir servi :
                            <strong>{{ number_format($validation->balance_used, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Dépenses :
                            <strong>{{ number_format($validation->expenses, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Payment factures :
                            <strong>{{ number_format($validation->payment_facture, 0, ',', ' ') }} F</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- III. MOUVEMENTS ÉLECTRONIQUES --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">III. Mouvements électroniques</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Vente TPE :
                            <strong>{{ number_format($validation->tpe_amount, 0, ',', ' ') }} F</strong>
                        </li>
                         <li class="list-group-item">
                            Vente OM :
                            <strong>{{ number_format($validation->om_amount, 0, ',', ' ') }} F</strong>
                        </li>
                         <li class="list-group-item">
                            Recharge OM :
                            <strong>{{ number_format($validation->om__recharge_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                                Recharges TPE :
                                <strong>{{ number_format($validation->tpe_recharge_amount, 2, ',', ' ') }} FCFA</strong>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        {{-- IV. RÉSULTAT --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">IV. Résultat</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Montant en caisse :
                            <strong>{{ number_format($validation->cash_amount, 0, ',', ' ') }} F</strong>
                        </li>
                        <li class="list-group-item">
                            Net à verser :
                            <strong class="text-primary">
                                {{ number_format($validation->net_to_deposit, 0, ',', ' ') }} F
                            </strong>
                        </li>
                        @php
                            $gap = $validation->cash_amount - $validation->net_to_deposit;
                        @endphp
                        <li class="list-group-item">
                            Écart :
                            <strong class="{{ $gap > 0 ? 'text-success' : ($gap < 0 ? 'text-danger' : 'text-muted') }}">
                                {{ ($gap > 0 ? '+' : '') . number_format($gap, 0, ',', ' ') }} F
                            </strong>
                        </li>
                    </ul>

                    <p class="mt-3 mb-0 text-muted">
                        Validé par <strong>{{ $validation->validator->name ?? '—' }}</strong>
                        le {{ $validation->validated_at?->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
