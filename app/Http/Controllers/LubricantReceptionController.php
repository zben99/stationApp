<?php

namespace App\Http\Controllers;

use App\Models\LubricantReception;
use App\Models\LubricantStock;
use App\Models\StationProduct;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LubricantReceptionController extends Controller
{
    public function index()
    {
        $receptions = LubricantReception::with('product', 'supplier')->latest()->get();
        return view('lubricants.index', compact('receptions'));
    }

    public function create()
    {
        $products = StationProduct::whereHas('stationCategory', fn($q) => $q->where('type', 'lubrifiant'))->get();
        $suppliers = Supplier::all();
        return view('lubricants.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date_reception' => 'required|date',
            'quantite' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        $reception = LubricantReception::create($request->all());

        // Stock
        $stock = LubricantStock::firstOrCreate([
            'station_product_id' => $request->station_product_id
        ]);
        $stock->quantite_actuelle += $request->quantite;
        $stock->save();

        // Optionnel : Mettre à jour le prix du produit
        $product = StationProduct::find($request->station_product_id);
        if ($request->filled('prix_vente')) {
            $product->price = $request->prix_vente;
            $product->save();
        }

        return redirect()->route('lubricant-receptions.index')->with('success', 'Réception enregistrée.');
    }

    public function edit(LubricantReception $lubricantReception)
    {
        $products = StationProduct::whereHas('category', fn($q) => $q->where('type', 'lubrifiant'))->get();
        $suppliers = Supplier::all();
        return view('lubricants.edit', compact('lubricantReception', 'products', 'suppliers'));
    }

    public function update(Request $request, LubricantReception $lubricantReception)
    {
        $request->validate([
            'station_product_id' => 'required|exists:station_products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date_reception' => 'required|date',
            'quantite' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        // Ajustement du stock
        $oldQty = $lubricantReception->quantite;
        $lubricantReception->update($request->all());

        $stock = LubricantStock::firstOrCreate(['station_product_id' => $request->station_product_id]);
        $stock->quantite_actuelle += ($request->quantite - $oldQty);
        $stock->save();

        // Mise à jour du prix du produit
        if ($request->filled('prix_vente')) {
            $product = StationProduct::find($request->station_product_id);
            $product->price = $request->prix_vente;
            $product->save();
        }

        return redirect()->route('lubricant-receptions.index')->with('success', 'Réception modifiée.');
    }

    public function destroy(LubricantReception $lubricantReception)
    {
        $stock = LubricantStock::where('station_product_id', $lubricantReception->station_product_id)->first();
        if ($stock) {
            $stock->quantite_actuelle -= $lubricantReception->quantite;
            $stock->save();
        }

        $lubricantReception->delete();

        return redirect()->route('lubricant-receptions.index')->with('success', 'Réception supprimée.');
    }
}
