 <!-- Sidebar -->
 <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
        <div class="sidebar-brand-icon rotate-n-15">
        </div>
        <div class="sidebar-brand-text mx-3">E-Station <sup>V1</sup></div>


    </a>
    @if(session('selected_station_id'))
    @php
        $station = \App\Models\Station::find(session('selected_station_id'));
    @endphp
    @if($station)
        <div class="text-center text-white small bg-primary bg-opacity-75 p-2 mx-3 my-2 rounded">
            <i class="fas fa-map-marker-alt me-1 text-warning"></i>
            <strong class="text-warning"> {{ $station->name }} - {{ $station->location }}</strong>
        </div>
    @endif
@endif





    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{route('dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Tableau de bord</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Configuration
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne"
            aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-user-lock fa-fw"></i>
            <span>Authentification</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('roles.index')}}">Gestion des rôles</a>
                <a class="collapse-item" href="{{route('users.index')}}">Gestion des utilisateurs</a>
            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-sliders-h fa-fw"></i>
            <span>Paramétrage Global</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item" href="{{route('stations.index')}}">Gestion des stations</a>


                @can('station-associate') <!-- Si l'utilisateur a le droit de gérer les associations -->
                     <a class="collapse-item" href="{{route('stations.associate')}}">Association Util. Stat.</a>
                @endcan


            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTree"
            aria-expanded="true" aria-controls="collapseTree">
            <i class="fas fa-cogs fa-fw"></i>
            <span>Paramétrage</span>
        </a>
        <div id="collapseTree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">


                @can('category-list')
                    <a class="collapse-item" href="{{route('categories.index')}}">Gestion des catégories</a>
                @endcan


                @can('supplier-list')
                    <a class="collapse-item" href="{{route('suppliers.index')}}">Gestion des fournisseurs</a>
                @endcan


                <a class="collapse-item" href="{{route('packagings.index')}}">Packeging </a>





            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsefour"
            aria-expanded="true" aria-controls="collapsefour">
            <i class="fas fas fa-gas-pump"></i>
            <span>Gestion du carburant</span>
        </a>
        <div id="collapsefour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">


                <a class="collapse-item" href="{{route('tanks.index')}}">Gestion des cuves</a>
                <a class="collapse-item" href="{{route('pumps.index')}}">Gestion des Pompes</a>

                <a class="collapse-item" href="{{route('transporters.index')}}">Gestion des transporters</a>

                <a class="collapse-item" href="{{route('drivers.index')}}">Gestion des Chauffeurs</a>


                <a class="collapse-item" href="{{route('fuel-receptions.index')}}">Dépotage Carburant</a>

            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse5"
            aria-expanded="true" aria-controls="collapse5">
            <i class="fas fas fa-oil-can"></i>
            <span>Lub / PEA / GAZ / Lampe</span>
        </a>
        <div id="collapse5" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">


                <a class="collapse-item" href="{{route('lubricant-products.index')}}">Gestion de Produits</a>

                <a class="collapse-item" href="{{route('lubricant-receptions.batch.index')}}">Approvisionnement</a>


            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link" href="{{route('purchase-invoices.index')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Factures Four. boutique</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse6"
            aria-expanded="true" aria-controls="collapse6">
            <i class="fas fa-money-bill-wave"></i>
            <span>Gestion des dépenses</span>
        </a>
        <div id="collapse6" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                @can('expense-category-list')
                    <a class="collapse-item" href="{{route('expense-categories.index')}}">Rubriques de dépenses</a>
                @endcan

                <a class="collapse-item" href="{{route('expenses.index')}}">Gestion des dépenses</a>


            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse7"
            aria-expanded="true" aria-controls="collapse7">
            <i class="fas fa-money-bill-wave"></i>
            <span>Gestion des clients</span>
        </a>
        <div id="collapse7" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('balances.summary')}}">Gestion des Avoirs</a>

                <a class="collapse-item" href="{{route('credit-topups.index')}}">Gestion des crédits</a>


            </div>
        </div>
    </li>





    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse8"
            aria-expanded="true" aria-controls="collapse8">
            <i class="fas fa-money-bill-wave"></i>
            <span>Caisse Journalière</span>
        </a>
        <div id="collapse8" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{route('fuel-indexes.index')}}">Relevés Carburant</a>

                <a class="collapse-item" href="{{route('daily-product-sales.index')}}">LUB/PEA/GAZ/LAMPES</a>


                <a class="collapse-item" href="{{route('daily-simple-revenues.index')}}">Boutique et lavages</a>

                <a class="collapse-item" href="{{route('daily-revenue-validations.index')}}">Validation de rotation</a>



            </div>
        </div>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">





</ul>
<!-- End of Sidebar -->
