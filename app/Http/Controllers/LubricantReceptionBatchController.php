<?php

namespace App\Http\Controllers;

use App\Models\LubricantReception;
use App\Models\LubricantReceptionBatch;
use App\Models\ProductPackaging;
use App\Models\StationCategory;
use App\Models\StationProduct;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'supplier',
        ])
            ->where('station_id', $stationId)
            ->orderByDesc('date_reception');

        // 🔍 Filtrage par catégorie
        if ($request->filled('category')) {
            $categoryId = $request->get('category');
            $query->whereHas('receptions.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // 🔍 Filtrage par rotation
        if ($request->filled('rotation')) {
            $query->where('rotation', $request->get('rotation'));
        }

        // 🔍 Filtrage par date
        if ($request->filled('date')) {
            $query->whereDate('date_reception', $request->get('date'));
        }

        $batches = $query->paginate(20)->appends($request->all());

        return view('lubricant_reception_batches.index', compact('batches', 'categories'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');



        $categories = StationCategory::where('station_id', $stationId)
            ->whereIn('type', ['lubrifiant'])
            ->orderBy('name')
            ->get();

        /*$stationProducts = StationProduct::whereIn('category_id', $categories)
            ->with('packagings')
            ->get();*/

        $suppliers = Supplier::where('station_id', $stationId)
            ->orderBy('name')
            ->get();

        return view('lubricant_reception_batches.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date_reception' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'supplier_id' => ['nullable', 'string', 'max:255'],
            'num_bc' => 'nullable|string|max:255',
            'num_bl' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.station_product_id' => 'required|exists:station_products,id',
            'products.*.product_packaging_id' => 'required|exists:station_product_packaging,id',
            'products.*.quantite' => 'required|numeric|min:0.01',
            'products.*.prix_vente' => 'nullable|numeric|min:0',
            'products.*.observations' => 'nullable|string|max:1000',
        ]);

        $stationId = session('selected_station_id');
        $supplierId = $this->resolveEntityId(
            Supplier::class, $request->supplier_id, $stationId
        );

        DB::beginTransaction();
        try {
            $batch = LubricantReceptionBatch::create([
                'station_id' => $stationId,
                'supplier_id' => $supplierId,
                'date_reception' => $request->date_reception,
                'rotation' => $request->rotation,
                'num_bc' => $request->num_bc,
                'num_bl' => $request->num_bl,
            ]);



            foreach ($data['products'] as $prod) {
                 $produitPack = ProductPackaging::findOrFail($prod['product_packaging_id']);
                LubricantReception::create([
                    'batch_id' => $batch->id,
                    'station_product_id' => $prod['station_product_id'],
                    'product_packaging_id' => $prod['product_packaging_id'],
                    'supplier_id' => $supplierId,
                    'date_reception' => $request->date_reception,
                    'quantite' => $prod['quantite'],
                    'prix_achat' =>  $produitPack->prix_achat ?? null,
                    'prix_vente' => $prod['prix_vente'] ?? null,
                    'observations' => $prod['observations'] ?? null,
                ]);

                LubricantReception::updateOrCreateStock(
                    $prod['station_product_id'],
                    $prod['product_packaging_id'],
                    $prod['quantite']
                );
            }

            DB::commit();

            return redirect()->route('lubricant-receptions.batch.index')->with('success', 'Réception enregistrée avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur : '.$e->getMessage()])->withInput();
        }
    }

    private function resolveEntityId(string $model, ?string $value, int $stationId): ?int
    {
        if (empty($value)) {
            return null;
        }

        // Id numérique existant
        if (is_numeric($value)) {
            return $model::findOrFail((int) $value)->id;
        }

        // Nouveau nom : on cherche d’abord dans la même station
        $record = $model::firstOrCreate(
            ['station_id' => $stationId, 'name' => trim($value)]
        );

        return $record->id;
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
            ->orderBy('name')
            ->pluck('id');

        $stationProducts = StationProduct::whereIn('category_id', $categories)
            ->with('productPackagings.packaging') // ← on utilise bien le modèle pivot avec ses relations
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::where('station_id', $stationId)
            ->orderBy('name')
            ->get();

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
            'rotation' => 'required|in:6-14,14-22,22-6',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'num_bc' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:lubricant_receptions,id',
            'products.*.station_product_id' => 'required|exists:station_products,id',
            'products.*.product_packaging_id' => 'required|exists:station_product_packaging,id',
            'products.*.quantite' => 'required|numeric|min:0.01',
            'products.*.observations' => 'nullable|string|max:1000',
        ]);

        // Mettre à jour les infos du batch
        $batch->update([
            'date_reception' => $request->date_reception,
            'rotation' => $request->rotation,
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

    public function getProductsByCategory($categoryId)
{
    $products = StationProduct::where('category_id', $categoryId)->get();
    return response()->json($products);
}

}
