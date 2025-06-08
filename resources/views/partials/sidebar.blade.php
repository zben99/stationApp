<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
        <div class="sidebar-brand-icon rotate-n-15"></div>
        <div class="sidebar-brand-text mx-3">E-Station <sup>V1</sup></div>
    </a>


    @if(session('selected_station_id'))
        @php
            $station = \App\Models\Station::find(session('selected_station_id'));
        @endphp
        @if($station)
            <div class="text-center text-white bg-primary bg-opacity-75 p-2 mx-3 my-2 rounded">
                <i class="fas fa-map-marker-alt me-1 text-warning"></i>
                <strong class="fs-5">
                    <a href="{{ route('station.selection', $station->id) }}" class="text-warning text-decoration-underline">
                        {{ $station->name }} - {{ $station->location }}
                    </a>
                </strong>
            </div>
        @endif
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    @can('dashboard-view')
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
        </a>
    </li>
    @endcan

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Configuration</div>

    <!-- Authentification -->
    @canany(['role-list','user-list'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAuth"
            aria-expanded="true" aria-controls="collapseAuth">
            <i class="fas fa-user-lock fa-fw"></i>
            <span>Authentification</span>
        </a>
        <div id="collapseAuth" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('role-list')
                    <a class="collapse-item" href="{{ route('roles.index') }}">Gestion des rôles</a>
                @endcan
                @can('user-list')
                    <a class="collapse-item" href="{{ route('users.index') }}">Gestion des utilisateurs</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Paramétrage Global -->
    @can('station-list')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGlobal"
            aria-expanded="true" aria-controls="collapseGlobal">
            <i class="fas fa-sliders-h fa-fw"></i>
            <span>Paramétrage Global</span>
        </a>
        <div id="collapseGlobal" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('stations.index') }}">Gestion des stations</a>
                @can('station-associate')
                    <a class="collapse-item" href="{{ route('stations.associate') }}">Association Util. Stat.</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan

    <!-- Paramétrage -->
    @canany(['category-list','supplier-list','packaging-list'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings"
            aria-expanded="true" aria-controls="collapseSettings">
            <i class="fas fa-cogs fa-fw"></i>
            <span>Paramétrage</span>
        </a>
        <div id="collapseSettings" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('category-list')
                    <a class="collapse-item" href="{{ route('categories.index') }}">Gestion des catégories</a>
                @endcan
                @can('supplier-list')
                    <a class="collapse-item" href="{{ route('suppliers.index') }}">Gestion des fournisseurs</a>
                @endcan
                @can('packaging-list')
                    <a class="collapse-item" href="{{ route('packagings.index') }}">Packaging</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Gestion du carburant -->
    @canany(['tank-list','view-pumps','view-transporters','view-drivers','fuel-reception-list'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFuel"
            aria-expanded="true" aria-controls="collapseFuel">
            <i class="fas fa-gas-pump"></i>
            <span>Gestion du carburant</span>
        </a>
        <div id="collapseFuel" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('tank-list')
                    <a class="collapse-item" href="{{ route('tanks.index') }}">Gestion des cuves</a>
                @endcan
                @can('view-pumps')
                    <a class="collapse-item" href="{{ route('pumps.index') }}">Gestion des Pompes</a>
                @endcan
                @can('view-transporters')
                    <a class="collapse-item" href="{{ route('transporters.index') }}">Gestion des transporteurs</a>
                @endcan
                @can('view-drivers')
                    <a class="collapse-item" href="{{ route('drivers.index') }}">Gestion des chauffeurs</a>
                @endcan
                @can('fuel-reception-list')
                    <a class="collapse-item" href="{{ route('fuel-receptions.index') }}">Dépotage Carburant</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Lub / PEA / GAZ / Lampe -->
    @canany(['view-lubricant-products','lubricant-reception-list'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLub"
            aria-expanded="true" aria-controls="collapseLub">
            <i class="fas fa-oil-can"></i>
            <span>Lub / PEA / GAZ / Lampe</span>
        </a>
        <div id="collapseLub" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('view-lubricant-products')
                    <a class="collapse-item" href="{{ route('lubricant-products.index') }}">Gestion de Produits</a>
                @endcan
                @can('lubricant-reception-list')
                    <a class="collapse-item" href="{{ route('lubricant-receptions.batch.index') }}">Approvisionnement</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Factures Fournisseur / Boutique -->
    @can('purchase-invoice-list')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('purchase-invoices.index') }}">
            <i class="fas fa-file-invoice"></i>
            <span>Factures Four. boutique</span>
        </a>
    </li>
    @endcan

    <!-- Gestion des dépenses -->
    @canany(['expense-category-list','expense-list'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExpenses"
            aria-expanded="true" aria-controls="collapseExpenses">
            <i class="fas fa-money-bill-wave"></i>
            <span>Gestion des dépenses</span>
        </a>
        <div id="collapseExpenses" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('expense-category-list')
                    <a class="collapse-item" href="{{ route('expense-categories.index') }}">Rubriques de dépenses</a>
                @endcan
                @can('expense-list')
                    <a class="collapse-item" href="{{ route('expenses.index') }}">Gestion des dépenses</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Gestion des clients -->
    @canany(['client-list','balance-view','credit-topup-list'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClients"
            aria-expanded="true" aria-controls="collapseClients">
            <i class="fas fa-users"></i>
            <span>Gestion des clients</span>
        </a>
        <div id="collapseClients" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('client-list')
                    <a class="collapse-item" href="{{ route('clients.index') }}">Gestion des clients</a>
                @endcan
                @can('balance-view')
                    <a class="collapse-item" href="{{ route('balances.summary') }}">Gestion des Avoirs</a>
                @endcan
                @can('credit-topup-list')
                    <a class="collapse-item" href="{{ route('credit-topups.index') }}">Gestion des crédits</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Caisse Journalière -->
    @canany(['view-fuel-indexes','view-daily-product-sales','view-daily-simple-revenues','view-daily-revenue-validations'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCash"
            aria-expanded="true" aria-controls="collapseCash">
            <i class="fas fa-calendar-check"></i>
            <span>Caisse Journalière</span>
        </a>
        <div id="collapseCash" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('view-fuel-indexes')
                    <a class="collapse-item" href="{{ route('fuel-indexes.index') }}">Relevés Carburant</a>
                @endcan
                @can('view-daily-product-sales')
                    <a class="collapse-item" href="{{ route('daily-product-sales.index') }}">LUB/PEA/GAZ/LAMPES</a>
                @endcan
                @can('view-daily-simple-revenues')
                    <a class="collapse-item" href="{{ route('daily-simple-revenues.index') }}">Boutique et lavages</a>
                @endcan
                @can('view-daily-revenue-validations')
                    <a class="collapse-item" href="{{ route('daily-revenue-validations.index') }}">Validation de rotation</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Divider -->
    <hr class="sidebar-divider">

</ul>
<!-- End of Sidebar -->


