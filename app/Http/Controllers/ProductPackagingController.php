<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use Illuminate\Http\Request;
use App\Models\LubricantStock;
use App\Models\StationProduct;
use App\Models\ProductPackaging;
use App\Models\LubricantReception;

class ProductPackagingController extends Controller
{
    public function index($productId)
    {
        $product = StationProduct::with(['productPackagings.lubricantStock', 'productPackagings.packaging'])->findOrFail($productId);

        return view('product_packagings.index', compact('product'));
    }
    public function create($productId)
    {
        $product = StationProduct::findOrFail($productId);
        $availablePackagings = Packaging::whereDoesntHave('products', function ($query) use ($productId) {
            $query->where('station_product_id', $productId);
        })->get();

        return view('product_packagings.create', compact('product', 'availablePackagings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'packaging_id' => 'required|exists:packagings,id',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0'
        ]);

        // Création du product-packaging
        $packaging = ProductPackaging::create([
            'station_product_id' => $request->station_product_id,
            'packaging_id' => $request->packaging_id,
            'price' => $request->price,
        ]);



           // 2. Création du stock lié au product-packaging
        LubricantStock::create([
            'station_product_id'    => $request->station_product_id,
            'product_packaging_id'  => $packaging->id, // 👈 Ici la correction
            'quantite_actuelle'     => $request->stock ?? 0,
        ]);


        return redirect()->route('product-packagings.index', $request->station_product_id)
                         ->with('success', 'Conditionnement associé avec succès.');
    }

    public function edit(ProductPackaging $productPackaging)
    {
        return view('product_packagings.edit', compact('productPackaging'));
    }

    public function update(Request $request, ProductPackaging $productPackaging)
    {
        $request->validate([
            'price' => 'nullable|numeric|min:0',
        ]);

        $productPackaging->update([
            'price' => $request->price,
        ]);

        return redirect()->route('product-packagings.index', $productPackaging->station_product_id)
                         ->with('success', 'Conditionnement mis à jour.');
    }

    public function destroy(ProductPackaging $productPackaging)
    {
        $productId = $productPackaging->station_product_id;
        $productPackaging->delete();

        return redirect()->route('product-packagings.index', $productId)
                         ->with('success', 'Conditionnement supprimé.');
    }
}
