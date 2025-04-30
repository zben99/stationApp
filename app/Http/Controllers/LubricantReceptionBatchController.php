<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\StationProduct;
use App\Models\StationCategory;
use App\Models\ProductPackaging;
use App\Models\LubricantReception;
use App\Models\LubricantReceptionBatch;

class LubricantReceptionBatchController extends Controller
{


    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::where('station_id', $stationId)
            ->whereIn('type', ['lubrifiant', 'pea', 'gaz', 'lampe'])
            ->get();

        $query = LubricantReceptionBatch::with([
            'receptions.product.stationCategory',
            'receptions.packaging.packaging',
            'supplier'
        ])->orderByDesc('date_reception');

        if ($request->filled('category')) {
            $categoryId = $request->get('category');
            $query->whereHas('receptions.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $batches = $query->paginate(20);

        return view('lubricant_reception_batches.index', compact('batches', 'categories'));
    }


    public function create()
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::where('station_id', $stationId)
            ->whereIn('type', ['lubrifiant', 'pea', 'gaz', 'lampe'])
            ->pluck('id');

        $stationProducts = StationProduct::whereIn('category_id', $categories)
            ->with('packagings')
            ->get();

        $suppliers = Supplier::where('station_id', $stationId)->get();

        return view('lubricant_reception_batches.create', compact('stationProducts', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_reception' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'num_bc' => 'nullable|string|max:255',
            'num_bl' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.station_product_id' => 'required|exists:station_products,id',
            'products.*.product_packaging_id' => 'required|exists:station_product_packaging,id',
            'products.*.quantite' => 'required|numeric|min:0.01',
            'products.*.prix_achat' => 'nullable|numeric|min:0',
            'products.*.prix_vente' => 'nullable|numeric|min:0',
            'products.*.observations' => 'nullable|string|max:1000',
        ]);

        $stationId = session('selected_station_id');

        $batch = LubricantReceptionBatch::create([
            'station_id' => $stationId,
            'supplier_id' => $request->supplier_id,
            'date_reception' => $request->date_reception,
            'num_bc' => $request->num_bc,
            'num_bl' => $request->num_bl,
        ]);

        foreach ($request->products as $prod) {
            LubricantReception::create([
                'batch_id' => $batch->id,
                'station_product_id' => $prod['station_product_id'],
                'product_packaging_id' => $prod['product_packaging_id'],
                'supplier_id' => $request->supplier_id,
                'date_reception' => $request->date_reception,
                'quantite' => $prod['quantite'],
                'prix_achat' => $prod['prix_achat'] ?? null,
                'prix_vente' => $prod['prix_vente'] ?? null,
                'observations' => $prod['observations'] ?? null,
            ]);

            LubricantReception::updateOrCreateStock(
                $prod['station_product_id'],
                $prod['product_packaging_id'],
                $prod['quantite']
            );
        }

        return redirect()->route('lubricant-receptions.batch.index')->with('success', 'Réception enregistrée avec succès.');
    }

    public function show(LubricantReceptionBatch $batch)
    {
        $batch->load('receptions.product', 'receptions.packaging.packaging', 'supplier');
        return view('lubricant_reception_batches.show', compact('batch'));
    }

    public function edit(LubricantReceptionBatch $batch)
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::where('station_id', $stationId)
            ->whereIn('type', ['lubrifiant', 'pea', 'gaz', 'lampe'])
            ->pluck('id');

        $stationProducts = StationProduct::whereIn('category_id', $categories)
            ->with('productPackagings.packaging') // ← on utilise bien le modèle pivot avec ses relations
            ->get();

        $suppliers = Supplier::where('station_id', $stationId)->get();

        $batch->load('receptions.product', 'receptions.packaging.packaging'); // ← pour la vue

        $packagingOptionsByProduct = [];

        foreach ($stationProducts as $product) {
            $packagingOptionsByProduct[$product->id] = $product->productPackagings->map(function ($pp) {
                return [
                    'id' => (int) $pp->id,
                    'name' => $pp->packaging->label ?? '-',
                    'unit' => $pp->packaging->unit ?? '',
                ];
            });
        }

        return view('lubricant_reception_batches.edit', compact(
            'batch',
            'stationProducts',
            'suppliers',
            'packagingOptionsByProduct'
        ));
    }


    public function update(Request $request, LubricantReceptionBatch $batch)
    {
        $request->validate([
            'date_reception' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'num_bc' => 'nullable|string|max:255',
            'num_bl' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:lubricant_receptions,id',
            'products.*.station_product_id' => 'required|exists:station_products,id',
            'products.*.product_packaging_id' => 'required|exists:station_product_packaging,id',
            'products.*.quantite' => 'required|numeric|min:0.01',
            'products.*.prix_achat' => 'nullable|numeric|min:0',
            'products.*.observations' => 'nullable|string|max:1000',
        ]);


        // Mettre à jour les infos du batch
        $batch->update([
            'date_reception' => $request->date_reception,
            'supplier_id' => $request->supplier_id,
            'num_bc' => $request->num_bc,
            'num_bl' => $request->num_bl,

        ]);

        foreach ($request->products as $prod) {
            $reception = LubricantReception::findOrFail($prod['id']);

            $ancienneQuantite = $reception->quantite;

            $reception->update([
                'station_product_id' => $prod['station_product_id'],
                'product_packaging_id' => $prod['product_packaging_id'],
                'supplier_id' => $request->supplier_id,
                'date_reception' => $request->date_reception,
                'quantite' => $prod['quantite'],
                'prix_achat' => $prod['prix_achat'] ?? null,
                'observations' => $prod['observations'] ?? null,
            ]);

            // Mise à jour du stock : retrait de l'ancienne quantité, ajout de la nouvelle
            LubricantReception::updateOrCreateStock(
                $prod['station_product_id'],
                $prod['product_packaging_id'],
                $prod['quantite'],
                $ancienneQuantite
            );
        }

        return redirect()->route('lubricant-receptions.batch.index')->with('success', 'Lot mis à jour avec succès.');
    }

    public function destroy(LubricantReceptionBatch $batch)
    {
        foreach ($batch->receptions as $rec) {
            LubricantReception::updateOrCreateStock(
                $rec->station_product_id,
                $rec->product_packaging_id,
                -$rec->quantite
            );
            $rec->delete();
        }

        $batch->delete();
        return redirect()->route('lubricant-receptions.batch.index')->with('success', 'Lot supprimé avec succès.');
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
