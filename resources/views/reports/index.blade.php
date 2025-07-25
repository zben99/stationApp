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
                        <i class="fas fa-gas-pump"></i> Contrôle stock carburant
                    </a>
                </div>

                  <div class="col-md-4">
                    <a href="{{ route('reports.supplies.fuel') }}" class="btn btn-outline-primary w-100 shadow-sm">
                        <i class="fas fa-gas-pump"></i> Approvisionnement carburant
                    </a>
                </div>

                <!-- Export recettes consolidées par période -->
                <div class="col-md-4">
                    <a href="{{ route('reports.consolidee.period') }}" class="btn btn-outline-success w-100 shadow-sm">
                        <i class="fas fa-calendar-alt"></i> Contrôle Caisse par période
                    </a>
                </div>



            </div>
            <br>
            <div class="row g-4">

                 <!-- Export recettes consolidées par rotation -->
                <div class="col-md-4">
                    <a href="{{ route('daily-revenue-report.index') }}" class="btn btn-outline-warning w-100 shadow-sm">
                        <i class="fas fa-clock"></i> Contrôle Caisse par rotation
                    </a>
                </div>

                <!-- Export ventes fuel -->
                <div class="col-md-4">
                    <a href="{{ route('fuel-reports.index') }}" class="btn btn-outline-danger w-100 shadow-sm">
                        <i class="fas fa-tachometer-alt"></i> Ventes carburant par rotation
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
