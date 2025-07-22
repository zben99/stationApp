<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\ExpenseCategory;
use App\Models\Packaging;
use App\Models\Station;
use App\Models\StationProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // 2. Création des catégories de produits par défaut
        $defaultCategories = [
            ['name' => 'Carburant', 'type' => 'fuel', 'is_active' => true],
            ['name' => 'LUBRIFIANTS', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => 'GAZ', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => 'LAMPES SOLAIRES', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => 'DIVERS', 'type' => 'lubrifiant', 'is_active' => true],
            ['name' => "Produits d'entretien auto", 'type' => 'lubrifiant', 'is_active' => true],
        ];

        foreach ($defaultCategories as $categoryData) {
            $station->categories()->create($categoryData);
        }

        // 3. Catégories de dépenses
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

        // 4. Données produits + packagings (extraites du fichier Excel)
        $productsData = [
            [
                'name' => 'MOTOR OIL 40',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => 'FÛT', 'price' => 497325],
                    ['label' => '20L', 'price' => 56150],
                    ['label' => '4L', 'price' => 12200],
                    ['label' => '1L', 'price' => 3150],
                ],
            ],
            [
                'name' => 'MOTOR OIL 50',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => 'FÛT', 'price' => 518700],
                    ['label' => '20L', 'price' => 60450],
                    ['label' => '4L', 'price' => 12550],
                    ['label' => '1L', 'price' => 3250],
                ],
            ],
            [
                'name' => 'QUARTZ 4X4',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 61400],
                    ['label' => '5L', 'price' => 19250],
                    ['label' => '1L', 'price' => 4350],
                ],
            ],
            [
                'name' => 'DACNIS 68',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 83546],
                ],
            ],
            [
                'name' => 'QUARTZ 2500',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 44000],
                ],
            ],
            [
                'name' => 'Rubia S (10)',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 60150],
                ],
            ],
            [
                'name' => 'Rubia S (40)',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 55150],
                    ['label' => '4L', 'price' => 12400],
                    ['label' => '1L', 'price' => 3850],
                ],
            ],
            [
                'name' => 'RUBIA 7400',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 85400],
                ],
            ],
            [
                'name' => 'FLUIDMATIC ATX',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 80200],
                    ['label' => '1L', 'price' => 4250],
                ],
            ],
            [
                'name' => 'Rubia 4400',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 61300],
                ],
            ],
            [
                'name' => 'AZOLA ZS 32',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 53279],
                ],
            ],
            [
                'name' => 'TRANSTEC5 85W140',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 63050],
                    ['label' => '1L', 'price' => 4000],
                ],
            ],
            [
                'name' => 'TRANSTEC5 80W90',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 59250],
                    ['label' => '1L', 'price' => 3750],
                ],
            ],
            [
                'name' => 'QUARTZ 9000',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '5L', 'price' => 23200],
                    ['label' => '1L', 'price' => 5100],
                ],
            ],
            [
                'name' => 'QUARTZ 9000 EXTRA',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '5L', 'price' => 37500],
                    ['label' => '1L', 'price' => 7500],
                ],
            ],
            [
                'name' => 'QUARTZ 7000',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '4L', 'price' => 16750],
                    ['label' => '1L', 'price' => 4350],
                ],
            ],
            [
                'name' => 'QUARTZ  5000',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '20L', 'price' => 58900],
                    ['label' => '4L', 'price' => 13150],
                    ['label' => '1L', 'price' => 4050],
                ],
            ],
            [
                'name' => 'INEO 5L',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '5L', 'price' => 24513],
                ],
            ],
            [
                'name' => 'RUBIA FLEET 400',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '4L', 'price' => 15050],
                    ['label' => '1L', 'price' => 4150],
                ],
            ],
            [
                'name' => 'RUBIA TIR 7400 4L',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '4L', 'price' => 17350],
                ],
            ],
            [
                'name' => 'COOLELF ECO',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '5L', 'price' => 13500],
                    ['label' => '1L', 'price' => 3500],
                ],
            ],
            [
                'name' => 'FLUIDE D3',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 4350],
                ],
            ],
            [
                'name' => 'FLUIDE XLD',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 4100],
                ],
            ],
            [
                'name' => 'HBF3 0,5L',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '0,5L', 'price' => 2550],
                ],
            ],
            [
                'name' => 'HBF4 0,25L',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '0,25L', 'price' => 1800],
                ],
            ],
            [
                'name' => 'MCO 4 TEMPS HI - PERF',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 3300],
                ],
            ],
            [
                'name' => 'SPECIAL 4 TEMPS HI - PERF',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 3900],
                ],
            ],
            [
                'name' => 'SPORT 4 TEMPS HI - PERF',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 4700],
                ],
            ],
            [
                'name' => 'SCOOTER 4 TEMPS HI - PERF',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 5100],
                ],
            ],
            [
                'name' => '2 TEMPS 1L',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1L', 'price' => 3600],
                ],
            ],
            [
                'name' => 'MULTIS 2 EP',
                'category' => 'LUBRIFIANTS',
                'variants' => [
                    ['label' => '1KG', 'price' => 4950],
                    ['label' => '50KG', 'price' => 206300],
                    ['label' => '200KG', 'price' => 694350],
                ],
            ],
            [
                'name' => 'LAVE GLACE',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 3000],
                    ['label' => '4L', 'price' => 9500],
                    ['label' => '5L', 'price' => 10200],
                ],
            ],
            [
                'name' => 'LIQUIDE DE REF',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 3000],
                    ['label' => '4L', 'price' => 10000],
                    ['label' => '5L', 'price' => 10900],
                ],
            ],
            [
                'name' => 'TRAITEMENT',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => 'ESSENCE', 'price' => 3750],
                    ['label' => 'DIESEL', 'price' => 3750],
                ],
            ],
            [
                'name' => 'SOS CREVAISON',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,4L', 'price' => 4000],
                ],
            ],
            [
                'name' => 'NETTOYANT RADIATEUR',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,3L', 'price' => 2400],
                ],
            ],
            [
                'name' => 'DETARTRANT RADIATEUR',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,25L', 'price' => 2600],
                ],
            ],
            [
                'name' => 'ANTI FUITE RADIATEUR',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,25L', 'price' => 4000],
                ],
            ],
            [
                'name' => 'FLUIDE MULTIFONCTION',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,25L', 'price' => 2550],
                ],
            ],
            [
                'name' => 'TE WASH',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => 'BOITE', 'price' => 3000],
                ],
            ],
            [
                'name' => 'SENTEUR DE France DESO.',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => 'FEUILLE', 'price' => 700],
                ],
            ],
            [
                'name' => 'DEO LITTLE BOTTLE',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => 'BOUTEILLE', 'price' => 2500],
                ],
            ],
            [
                'name' => 'SENTEUR DE France DIFF.',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => 'DIFFUSEUR', 'price' => 3000],
                ],
            ],
            [
                'name' => 'EAU DE BATTERIE',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 600],
                ],
            ],
            [
                'name' => 'ACIDE DE BATTERIE',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 750],
                ],
            ],
            [
                'name' => 'NETTOYANT JANTES',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 2500],
                ],
            ],
            [
                'name' => 'NETTOYANT MOTEUR',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,5L', 'price' => 3000],
                ],
            ],
            [
                'name' => 'NETTOYANT TABLEAU',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '0,5L', 'price' => 2300],
                ],
            ],
            [
                'name' => 'SHAMPOOING AUTO PLUS',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 5000],
                    ['label' => '0,25L', 'price' => 2400],
                ],
            ],
            [
                'name' => 'AD BLUE',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '5L', 'price' => 12500],
                ],
            ],
            [
                'name' => 'LHM PLUS',
                'category' => 'Produits d\'entretien auto',
                'variants' => [
                    ['label' => '1L', 'price' => 2600],
                ],
            ],
            [
                'name' => 'LAMPES SOLAIRES S30',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'S30', 'price' => 6500],
                ],
            ],
            [
                'name' => 'LAMPES SOLAIRES S3',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'S3', 'price' => 4200],
                ],
            ],
            [
                'name' => 'LAMPES SOLAIRES A2',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'A2', 'price' => 2900],
                ],
            ],
            [
                'name' => 'ONE SUN SHINE',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'ONE', 'price' => 3900],
                ],
            ],
            [
                'name' => 'POCKET SUN SHINE',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'POCKET', 'price' => 2900],
                ],
            ],
            [
                'name' => 'LAMPES SOLAIRES T200',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'T200', 'price' => 17000],
                ],
            ],
            [
                'name' => 'LAMPES SOLAIRES S500',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'S500', 'price' => 17000],
                ],
            ],
            [
                'name' => 'LAMPES SOLAIRES D20',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'D20', 'price' => 59500],
                ],
            ],
            [
                'name' => 'RADIO D20',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'RADIO', 'price' => 11900],
                ],
            ],
            [
                'name' => 'SUN SHINE',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'SUN SHINE 150', 'price' => 11900],
                    ['label' => 'SUN SHINE 300', 'price' => 15500],
                    ['label' => 'LITTLE SUN SHINE', 'price' => 3900],
                    ['label' => 'FAMILY SUN SHINE', 'price' => 14900],
                    ['label' => 'HOME SUN SHINE', 'price' => 57200],
                ],
            ],
            [
                'name' => 'LAMPE ULITIUM',
                'category' => 'LAMPES SOLAIRES',
                'variants' => [
                    ['label' => 'U1', 'price' => 28000],
                    ['label' => 'U2', 'price' => 45000],
                    ['label' => 'U3', 'price' => 62000],
                    ['label' => 'U4', 'price' => 80000],
                ],
            ],
            [
                'name' => 'GAZ 6KG',
                'category' => 'GAZ',
                'variants' => [
                    ['label' => 'Charge 06 Kg', 'price' => 2000],
                    ['label' => 'Consignation 06 Kg', 'price' => 13500],
                ],
            ],
            [
                'name' => 'GAZ 12,5KG',
                'category' => 'GAZ',
                'variants' => [
                    ['label' => 'Charge 12,5 Kg', 'price' => 5500],
                    ['label' => 'Consignation 12,5 Kg', 'price' => 16500],
                ],
            ],
            [
                'name' => 'GAZ 38KG',
                'category' => 'GAZ',
                'variants' => [
                    ['label' => 'Charge 38 Kg', 'price' => 34200],
                    ['label' => 'Consignation 38 Kg', 'price' => 18000],
                ],
            ],
            [
                'name' => 'TELIMAGAZ',
                'category' => 'GAZ',
                'variants' => [
                    ['label' => 'TELIMAGAZ', 'price' => 25500],
                ],
            ],
            [
                'name' => 'ZAMA',
                'category' => 'DIVERS',
                'variants' => [
                    ['label' => 'Z 10 000', 'price' => 10700],
                    ['label' => 'Z 25 000', 'price' => 25700],
                    ['label' => 'Z 50 000', 'price' => 50700],
                ],
            ],
        ];

        foreach ($productsData as $prod) {
            $category = $station->categories()->where('name', $prod['category'])->first();
            $product = StationProduct::create([
                'station_id' => $station->id,
                'name' => $prod['name'],
                'category_id' => $category->id,
                'code' => strtoupper(Str::slug($prod['name'], '_')),
                'price' => $prod['variants'][0]['price'] ?? 0,
                'is_active' => true,
            ]);

            foreach ($prod['variants'] as $variant) {
                $packaging = Packaging::firstOrCreate(
                    [
                        'station_id' => $station->id,
                        'label' => $variant['label'],
                    ],
                    [
                        'quantity' => 1,
                        'unit' => 'L',
                        'type' => 'bidon',
                    ]
                );

                $product->packagings()->attach($packaging->id, [
                    'price' => $variant['price'],
                ]);
            }
        }



        // 5. Création des produits FUEL
        $fuelCategory = $station->categories()->where('name', 'Carburant')->first();

        if ($fuelCategory) {
            // clé = nom du produit, valeur = [prix_vente, prix_achat]
            $fuelProducts = [
                'SUPER SANS PLOMB' => [850, 831],
                'GASOIL' => [675, 658],
            ];

            foreach ($fuelProducts as $name => [$prix_vente, $prix_achat]) {
                StationProduct::create([
                    'station_id' => $station->id,
                    'name' => $name,
                    'category_id' => $fuelCategory->id,
                    'price' => $prix_vente,
                    'prix_achat' => $prix_achat,
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
