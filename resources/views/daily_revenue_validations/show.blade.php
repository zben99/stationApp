<x-app-layout>
    <x-slot name="header">🧾 Détails de la validation du {{ $validation->formatted_date }} ({{ $validation->rotation_label }})</x-slot>

    <div class="mb-3">
        <a href="{{ route('daily-revenue-validations.index') }}" class="btn btn-secondary">
            ← Retour à la liste
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">I. Encaissements</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Carburants : <strong>{{ number_format($validation->fuel_amount, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Produits (LUB, PEA, etc.) : <strong>{{ number_format($validation->product_amount, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Boutique / Lavage : <strong>{{ number_format($validation->shop_amount, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Remboursement crédit : <strong>{{ number_format($validation->credit_repaid, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Avoir perçu : <strong>{{ number_format($validation->balance_received, 0, ',', ' ') }} F</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">II. Décaissements</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Crédit accordé : <strong>{{ number_format($validation->credit_received, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Avoir servi : <strong>{{ number_format($validation->balance_used, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Dépenses : <strong>{{ number_format($validation->expenses, 0, ',', ' ') }} F</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">III. Mouvements électroniques</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Recharge TPE : <strong>{{ number_format($validation->tpe_amount, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Recharge OM : <strong>{{ number_format($validation->om_amount, 0, ',', ' ') }} F</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">IV. Résultat</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Montant en caisse : <strong>{{ number_format($validation->cash_amount, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">Net à verser : <strong class="text-primary">{{ number_format($validation->net_to_deposit, 0, ',', ' ') }} F</strong></li>
                        <li class="list-group-item">
                            Écart :
                            @php
                                $ecart = $validation->cash_amount - $validation->net_to_deposit;
                            @endphp
                            <strong class="{{ $ecart > 0 ? 'text-success' : ($ecart < 0 ? 'text-danger' : 'text-muted') }}">
                                {{ ($ecart > 0 ? '+' : '') . number_format($ecart, 0, ',', ' ') }} F
                            </strong>
                        </li>
                    </ul>
                    <p class="mt-3 mb-0 text-muted">
                        Validé par <strong>{{ $validation->validator->name ?? '—' }}</strong> le {{ $validation->validated_at?->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
