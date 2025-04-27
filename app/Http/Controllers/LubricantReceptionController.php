<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\StationProduct;
use App\Models\StationCategory;
use App\Models\ProductPackaging;
use App\Models\LubricantReception;

class LubricantReceptionController extends Controller
{
    public function index()
    {


        $receptions = LubricantReception::with('product', 'supplier', 'packaging')->latest()->paginate(20);

        return view('lubricant_receptions.index', compact('receptions'));
    }

    public function create()
    {


        $stationId = session('selected_station_id');

        // Récupérer les catégories lubrifiant et pea
        $categories = StationCategory::where('station_id', $stationId)
            ->whereIn('type', ['lubrifiant', 'pea'])
            ->pluck('id'); // On récupère juste les IDs

        // Récupérer les produits correspondants
        $stationProducts = StationProduct::whereIn('category_id', $categories)->get();

        // Récupérer les fournisseurs liés à la station
        $suppliers = Supplier::where('station_id', $stationId)->get();

        // Pour les packagings : on ne récupère rien ici, on ira chercher par AJAX selon le produit choisi
        $packagings = []; // vide au début

        return view('lubricant_receptions.create', compact('stationProducts', 'suppliers', 'packagings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'product_packaging_id' => 'required|exists:station_product_packaging,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date_reception' => 'required|date',
            'quantite' => 'required|numeric|min:0.01',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string|max:1000',
        ]);

        // 1. On enregistre la réception
        $reception = LubricantReception::create($validated);

        // 2. Mise à jour automatique du stock
        LubricantReception::updateOrCreateStock(
            $validated['station_product_id'],
            $validated['product_packaging_id'],
            $validated['quantite']
        );

        return redirect()->route('lubricant-receptions.index')->with('success', 'Réception enregistrée avec succès.');
    }

    public function show(LubricantReception $lubricantReception)
    {
        return view('lubricant_receptions.show', compact('lubricantReception'));
    }

    public function edit(LubricantReception $lubricantReception)
    {
        $stationId = session('selected_station_id');

        // Récupérer les catégories lubrifiant et pea
        $categories = StationCategory::where('station_id', $stationId)
            ->whereIn('type', ['lubrifiant', 'pea'])
            ->pluck('id');

        // Produits de la station
        $stationProducts = StationProduct::whereIn('category_id', $categories)->get();

        // Fournisseurs de la station
        $suppliers = Supplier::where('station_id', $stationId)->get();

        // Packagings liés au produit actuel, MÊME format que getPackagings
        $packagings = ProductPackaging::with('packaging')
            ->where('station_product_id', $lubricantReception->station_product_id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->packaging->label,
                    'unit' => $item->packaging->unit,
                ];
            });

        return view('lubricant_receptions.edit', compact('lubricantReception', 'stationProducts', 'suppliers', 'packagings'));
    }




    public function update(Request $request, LubricantReception $lubricantReception)
    {
        $validated = $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'product_packaging_id' => 'required|exists:station_product_packaging,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date_reception' => 'required|date',
            'quantite' => 'required|numeric|min:0.01',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string|max:1000',
        ]);

        // 1. Calculer la différence de la quantité (ancienne vs nouvelle)
        $previousQuantity = $lubricantReception->quantite;

        // 2. Mettre à jour la réception
        $lubricantReception->update($validated);

        // 3. Mettre à jour le stock avec la différence
        LubricantReception::updateOrCreateStock(
            $validated['station_product_id'],
            $validated['product_packaging_id'],
            $validated['quantite'],
            $previousQuantity // Passer l'ancienne quantité pour ajuster correctement le stock
        );

        return redirect()->route('lubricant-receptions.index')->with('success', 'Réception mise à jour avec succès.');
    }



    public function destroy(LubricantReception $lubricantReception)
    {
        // 1. Avant de supprimer, décrémenter le stock
        LubricantReception::updateOrCreateStock(
            $lubricantReception->station_product_id,
            $lubricantReception->product_packaging_id,
            -$lubricantReception->quantite // on décrémente en négatif
        );

        // 2. Ensuite supprimer
        $lubricantReception->delete();

        return redirect()->route('lubricant-receptions.index')->with('success', 'Réception supprimée avec succès.');
    }



    public function getPackagings($productId)
    {
        $packagings = ProductPackaging::with('packaging')
        ->where('station_product_id', $productId)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->packaging->label,
                'unit' => $item->packaging->unit,
            ];
        });

        return response()->json($packagings);
    }
}
