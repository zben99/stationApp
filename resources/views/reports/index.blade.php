<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dark">
            {{ __('Rapports et exports') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row g-4">

                <!-- Rapport de stock carburant -->
                <div class="col-md-4">
                    <a href="{{ route('exports.fuel-stock.index') }}" class="btn btn-outline-primary w-100 shadow-sm">
                        <i class="fas fa-file-invoice"></i> Contrôle stock carburant
                    </a>
                </div>

                <!-- Export ventes fuel -->
                <div class="col-md-4">
                    <a href="{{ route('fuel-reports.index') }}" class="btn btn-outline-success w-100 shadow-sm">
                        <i class="fas fa-chart-line"></i> Ventes carburant
                    </a>
                </div>

                <!-- Export ventes produits (lubrifiants, gaz, etc.) -->
                <div class="col-md-4">
                    <a href="{{ route('fuel-stock-controls.index') }}" class="btn btn-outline-warning w-100 shadow-sm">
                        <i class="fas fa-box"></i> Ventes produits divers
                    </a>
                </div>

            </div>
            <br>
             <div class="row g-4">

                <!-- Export général en Excel -->
                <div class="col-md-4">
                    <a href="{{ route('fuel-stock-controls.index') }}" class="btn btn-outline-secondary w-100 shadow-sm">
                        <i class="fas fa-file-excel"></i> Export Excel complet
                    </a>
                </div>

                <!-- Historique des mouvements de cuve -->
                <div class="col-md-4">
                    <a href="{{ route('fuel-stock-controls.index') }}" class="btn btn-outline-dark w-100 shadow-sm">
                        <i class="fas fa-history"></i> Historique des stocks cuves
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
