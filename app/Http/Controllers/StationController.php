<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Packaging;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StationProduct;
use App\Models\ExpenseCategory;
use App\Models\ProductPackaging;
use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;

class StationController extends Controller
{
    public function index(Request $request)
    {

        $data = Station::latest()->paginate(5);

        return view('stations.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        return view('stations.create');
    }


    public function store(StoreStationRequest $request)
    {
        // 1. Création de la station
        $station = Station::create($request->validated());

        // 2. Catégories par défaut à associer
        $defaultCategories = [
            ['name' => 'Carburant', 'type' => 'fuel', 'is_active' => true],
            ['name' => 'GAZ', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => 'Lampes', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => 'Lubrifiant', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => "Produits d'Entretien Auto (PEA)", 'type' => 'lubrifiant', 'is_active' => true],
        ];

        // 3. Association à la station
        foreach ($defaultCategories as $categoryData) {
            $station->categories()->create($categoryData);
        }


            // 4. Création des catégories DÉPENSES
            $defaultExpenseCategories = [
                'Eau',
                'Frais de communication + Forfait internet',
                "Achat Monocup, sucre, bombone d'eau, cuillère, couvercle",
                'Courses station',
                'Frais de versement, photocopies, parking',
                'Entretien station (Vidanges+Jardinier)',
                'Papeterie, Fournitures de bureau',
                'Emballage perdu',
                'Frais comptable',
                'Gardiennage',
                'Transport marchandises',
                'Frais de transport gaz',
                'Achat tenues et chaussures de sécurité',
                'Divers',
            ];

            foreach ($defaultExpenseCategories as $name) {
                ExpenseCategory::create([
                    'station_id' => $station->id,
                    'name' => $name,
                    'description' => null,
                    'is_active' => true,
                ]);
            }

            // 5. Création des packagings
            $packagings = [
                ['label' => 'VRAC', 'quantity' => -1, 'unit' => 'L', 'type' => 'vrac'],
                ['label' => 'FUT 200L', 'quantity' => 200, 'unit' => 'L', 'type' => 'bidon'],
                ['label' => 'Bidon 20L', 'quantity' => 20, 'unit' => 'L', 'type' => 'bidon'],
                ['label' => 'Bidon 4/5L', 'quantity' => 5, 'unit' => 'L', 'type' => 'bidon'],
                ['label' => 'Bidon 2L', 'quantity' => 2, 'unit' => 'L', 'type' => 'bidon'],
                ['label' => 'Bidon 1L / 0.5L', 'quantity' => 1, 'unit' => 'L', 'type' => 'bidon'],
                ['label' => 'Graisse 1kg', 'quantity' => 1, 'unit' => 'Kg', 'type' => 'graisse'],
                ['label' => 'Graisse 50kg', 'quantity' => 50, 'unit' => 'Kg', 'type' => 'graisse'],
                ['label' => 'Graisse 200kg', 'quantity' => 200, 'unit' => 'Kg', 'type' => 'graisse'],
            ];

            // Création ou récupération des packagings
            $createdPackagings = [];
            foreach ($packagings as $data) {
                 $data['station_id'] = session('selected_station_id');
                $createdPackagings[$data['label']] = Packaging::firstOrCreate($data);
            }

            // 6. Récupération de la catégorie "Lubrifiant"
            $lubCategory = $station->categories()->where('name', 'Lubrifiant')->first();

            // 7. Produits avec packaging et prix
            $products = [
                // VRAC
                ['name' => 'AZOLA ZS 32', 'packaging' => 'VRAC', 'price' => 2400],
                ['name' => 'QUARTZ 5000', 'packaging' => 'VRAC', 'price' => 2512],
                ['name' => '2TZ', 'packaging' => 'FUT 200L', 'price' => 1600],
                ['name' => 'MOTOR OIL 40', 'packaging' => 'VRAC', 'price' => 497325],
                ['name' => 'MOTOR OIL 50', 'packaging' => 'VRAC', 'price' => 518700],

                // 20L
                ['name' => 'QUARTZ 4X4', 'packaging' => 'Bidon 20L', 'price' => 61400],
                ['name' => 'DACNIS 68', 'packaging' => 'Bidon 20L', 'price' => 83546],

                // 4/5L
                ['name' => 'QUARTZ 9000', 'packaging' => 'Bidon 4/5L', 'price' => 23200],
                ['name' => 'MOTOR OIL 50 4L', 'packaging' => 'Bidon 4/5L', 'price' => 13500],

                // 1L/0.5L
                ['name' => 'FLUIDE D3', 'packaging' => 'Bidon 1L / 0.5L', 'price' => 4350],
                ['name' => 'MOTOR OIL 40', 'packaging' => 'Bidon 1L / 0.5L', 'price' => 3150],

                // Graisses
                ['name' => 'MULTIS 2', 'packaging' => 'Graisse 1kg', 'price' => 4950],
                ['name' => 'MULTIS 50kg', 'packaging' => 'Graisse 50kg', 'price' => 206300],
                ['name' => 'MULTIS 200kg', 'packaging' => 'Graisse 200kg', 'price' => 694350],
            ];

            // 8. Création et association des produits
            foreach ($products as $prod) {
                // Création du produit lié à la station
                $stationProduct = StationProduct::create([
                    'station_id' => $station->id,
                    'name' => $prod['name'],
                    'category_id' => $lubCategory->id,
                    'code' => strtoupper(Str::slug($prod['name'], '_')),
                    'price' => $prod['price'], // prix par défaut
                ]);

                // Création de l'association packaging + prix via pivot
                $packaging = $createdPackagings[$prod['packaging']];
                $stationProduct->packagings()->attach($packaging->id, ['price' => $prod['price']]);
            }




        // 9. Création des produits PEA + packaging auto


       $peaCategory = $station->categories()->where('name', "Produits d'Entretien Auto (PEA)")->first();

        if ($peaCategory) {
            $peaProducts = [
                "LAVE GLACE 1L" => 3000,
                "LAVE GLACE 4L" => 8300,
                "LAVE GLACE 5L" => 10200,
                "LIQUIDE DE REF 1L" => 3000,
                "LIQUIDE DE REF 4L" => 8750,
                "LIQUIDE DE REF 5L" => 10900,
                "TRAITEMENT ESSENCE" => 3500,
                "TRAITEMENT DIESEL" => 3500,
                "SOS CREVAISON" => 4000,
                "NETTOYANT RADIATEUR" => 2400,
                "DETARTRANT RADIATEUR" => 2600,
                "STOP FUITE RADIATEUR" => 2600,
                "FLUIDE MULTIFONCTION" => 2550,
                "TE WASH" => 3000,
                "SENTEUR DE France DESO." => 700,
                "DEO LITTLE BOTTLE" => 2500,
                "SENTEUR DE France DIFF." => 3000,
                "EAU DE BATTERIE" => 600,
                "ACIDE DE BATTERIE" => 750,
                "NETTOYANT JANTES" => 2500,
                "NETTOYANT MOTEUR" => 3000,
                "NETTOYANT TABLEAU" => 2300,
                "SHAMPOOING AUTO PLUS 1L" => 5000,
                "SHAMPOOING AUTO PLUS" => 2400,
                "AD BLUE" => 12500,
                "LHM PLUS" => 2600,
            ];

            foreach ($peaProducts as $name => $price) {
                // Créer le produit sans packaging
                StationProduct::create([
                    'station_id' => $station->id,
                    'name' => $name,
                    'category_id' => $peaCategory->id,
                    'price' => $price,
                    'code' => strtoupper(Str::slug($name, '_')),
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('stations.index')->with('success', 'Station ajoutée avec succès.');
    }

    public function edit(Station $station)
    {
        return view('stations.edit', compact('station'));
    }

    public function update(UpdateStationRequest $request, Station $station)
    {
        $station->update($request->validated());

        return redirect()->route('stations.index')->with('success', 'Station mise à jour avec succès.');
    }

    public function destroy(Station $station)
    {
        $station->delete();

        return redirect()->route('stations.index')->with('success', 'Station supprimée.');
    }
}
