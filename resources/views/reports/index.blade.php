<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">
            {{ __('Rapports & Exports') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">

            {{-- 🔁 VENTES & RECETTES --}}
            <h5 class="text-muted mb-3 mt-2">💰 Ventes & Recettes</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="{{ route('fuel-reports.index') }}" class="btn btn-outline-danger w-100 shadow-sm">
                        <i class="fas fa-gas-pump"></i> Ventes carburant par rotation
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('reports.consolidee.period') }}" class="btn btn-outline-success w-100 shadow-sm">
                        <i class="fas fa-calendar-alt"></i> Caisse consolidée par période
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('daily-revenue-report.index') }}" class="btn btn-outline-warning w-100 shadow-sm">
                        <i class="fas fa-clock"></i> Caisse par rotation
                    </a>
                </div>
            </div>

            {{-- 📦 STOCKS & APPROVISIONNEMENTS --}}
            <h5 class="text-muted mb-3 mt-5">📦 Stocks & Approvisionnements</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="{{ route('exports.fuel-stock.index') }}" class="btn btn-outline-primary w-100 shadow-sm">
                        <i class="fas fa-boxes"></i> Stock carburant (contrôle)
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('reports.supplies.fuel') }}" class="btn btn-outline-primary w-100 shadow-sm">
                        <i class="fas fa-truck"></i> Approvisionnement carburant
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('reports.supplies.lubricants') }}" class="btn btn-outline-primary w-100 shadow-sm">
                        <i class="fas fa-truck"></i> Appro Lub / PEA / Gaz / Autres
                    </a>
                </div>
            </div>

            {{-- 🛢️ DÉPOTAGES --}}
            <h5 class="text-muted mb-3 mt-5">🛢️ Dépotages</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="{{ route('reports.depotage.cumule') }}" class="btn btn-outline-secondary w-100 shadow-sm">
                        <i class="fas fa-oil-can"></i> Dépotages carburant (cumulé)
                    </a>
                </div>
            </div>

            {{-- 👥 CLIENTS & CRÉDITS --}}
            <h5 class="text-muted mb-3 mt-5">👥 Clients & Crédits</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="{{ route('reports.clients.credits') }}" class="btn btn-outline-success w-100 shadow-sm">
                        <i class="fas fa-hand-holding-usd"></i> Crédits clients
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
