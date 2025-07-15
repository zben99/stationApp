<?php

namespace App\Http\Controllers;

use App\Models\LubricantStock;
use App\Models\Packaging;
use App\Models\ProductPackaging;
use App\Models\StationProduct;
use Illuminate\Http\Request;

class ProductPackagingController extends Controller
{
    public function index($productId)
    {
        // Récupérer le produit avec ses conditionnements et stocks
        $product = StationProduct::with(['productPackagings.lubricantStock', 'productPackagings.packaging'])->findOrFail($productId);

        return view('product_packagings.index', compact('product'));
    }

    public function create($productId)
    {
        $product = StationProduct::findOrFail($productId);
        // Récupérer les conditionnements disponibles non encore associés à ce produit
        $availablePackagings = Packaging::whereDoesntHave('products', function ($query) use ($productId) {
            $query->where('station_product_id', $productId);
        })->get();

        return view('product_packagings.create', compact('product', 'availablePackagings'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'packaging_id' => 'required|exists:packagings,id',
            'prix_achat' => 'nullable|numeric|min:0',  // Validation du prix d'achat
            'price' => 'nullable|numeric|min:0',       // Validation du prix de vente
            'stock' => 'nullable|integer|min:0',       // Validation du stock
        ]);

        // Création du product-packaging
        $packaging = ProductPackaging::create([
            'station_product_id' => $request->station_product_id,
            'packaging_id' => $request->packaging_id,
            'prix_achat' => $request->prix_achat,  // Stocker le prix d'achat
            'price' => $request->price,            // Stocker le prix de vente
        ]);

        // Création du stock lié au product-packaging
        LubricantStock::create([
            'station_product_id' => $request->station_product_id,
            'product_packaging_id' => $packaging->id,  // Associer au bon packaging
            'quantite_actuelle' => $request->stock ?? 0, // Utiliser la quantité donnée
        ]);

        return redirect()->route('product-packagings.index', $request->station_product_id)
            ->with('success', 'Conditionnement associé avec succès.');
    }

    public function edit($productId, $productPackagingId)
    {
        // Récupérer le produit et le conditionnement
        $product = StationProduct::findOrFail($productId);
        $productPackaging = ProductPackaging::where('station_product_id', $productId)
                                        ->where('id', $productPackagingId)
                                        ->firstOrFail();

        // Retourner la vue d'édition avec les données
        return view('product_packagings.edit', compact('product', 'productPackaging'));
    }


public function update(Request $request, $productId, $productPackagingId)
{
    // Validation des données
    $request->validate([
        'prix_achat' => 'nullable|numeric|min:0',
        'price' => 'nullable|numeric|min:0',
        'stock' => 'nullable|integer|min:0',
    ]);

    // Récupérer le produit et le conditionnement à modifier
    $product = StationProduct::findOrFail($productId);
    $productPackaging = ProductPackaging::where('station_product_id', $productId)
                                       ->where('id', $productPackagingId)
                                       ->firstOrFail();

    // Mise à jour du product-packaging
    $productPackaging->update([
        'prix_achat' => $request->prix_achat,
        'price' => $request->price,
    ]);

    // Vérifier si un stock existe pour ce product-packaging avant de le mettre à jour
    $lubricantStock = $productPackaging->lubricantStock;  // Obtenir l'objet lubricantStock

    if ($lubricantStock) {
        // Mise à jour du stock si le stock existe
        $lubricantStock->update([
            'quantite_actuelle' => $request->stock ?? $lubricantStock->quantite_actuelle,  // Mettre à jour la quantité si stock fourni, sinon garder l'ancienne
        ]);
    } else {
        // Si aucun stock n'existe, créer un nouveau stock
        LubricantStock::create([
            'station_product_id' => $productId,
            'product_packaging_id' => $productPackagingId,
            'quantite_actuelle' => $request->stock ?? 0,  // Définir le stock à zéro si non fourni
        ]);
    }

    // Retourner vers la page d'index avec un message de succès
    return redirect()->route('product-packagings.index', $product->id)
                     ->with('success', 'Conditionnement mis à jour avec succès');
}



public function destroy($productId, $productPackagingId)
{
    // Récupérer le conditionnement à partir de l'ID
    $productPackaging = ProductPackaging::where('station_product_id', $productId)
                                       ->where('id', $productPackagingId)
                                       ->firstOrFail();

    // Supprimer le product-packaging
    $productPackaging->delete();

    // Rediriger vers la liste des conditionnements avec un message de succès
    return redirect()->route('product-packagings.index', $productId)
                     ->with('success', 'Conditionnement supprimé avec succès.');
}

}
