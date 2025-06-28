<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StationProduct;
use App\Models\StationCategory;
use Illuminate\Validation\Rule;

class LubricantProductController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $categoryId = $request->input('category');

        $query = StationProduct::with('stationCategory', 'station')
            ->where('station_id', $stationId)
            ->whereHas('stationCategory', function ($q) {
                $q->whereIn('name', ['LUBRIFIANTS', 'Produits d\'entretien auto', 'GAZ', 'LAMPES SOLAIRES','DIVERS']);
            });



        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->paginate(10);

        $categories = StationCategory::whereIn('name', ['LUBRIFIANTS', 'Produits d\'entretien auto', 'GAZ', 'LAMPES SOLAIRES','DIVERS'])
        ->where('station_id', $stationId)
        ->get();


        return view('lubricants.products.index', compact('products', 'categories'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $stationId = session('selected_station_id');

         $categories = StationCategory::whereIn('name', ['LUBRIFIANTS', 'Produits d\'entretien auto', 'GAZ', 'LAMPES SOLAIRES','DIVERS'])
          ->where('station_id', $stationId)
         ->get();


        return view('lubricants.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $stationId = session('selected_station_id');

        /* ───── Validation ───── */
        $validated = $request->validate([
            'category_id' => ['required', 'exists:station_categories,id'],
            'code'        => [
                'required', 'string', 'max:30',
                // unique PAR station
                Rule::unique('station_products')->where(fn ($q) =>
                    $q->where('station_id', $stationId)
                ),
            ],
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['nullable', 'numeric', 'min:0'],
        ]);

        /* ───── Création ───── */
        StationProduct::create([
            'station_id'  => $stationId,
            'category_id' => $validated['category_id'],
            'code'        => $validated['code'],
            'name'        => $validated['name'],
            'price'       => $validated['price'] ?? 0,
        ]);

        return redirect()
            ->route('lubricant-products.index')
            ->with('success', 'Produit ajouté avec succès.');
    }


    public function edit(StationProduct $lubricantProduct)
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::whereIn('name', ['LUBRIFIANTS', 'Produits d\'entretien auto', 'GAZ', 'LAMPES SOLAIRES','DIVERS'])
         ->where('station_id', $stationId)
         ->get();


        return view('lubricants.products.edit', [
            'product' => $lubricantProduct,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, StationProduct $lubricantProduct)
    {
        $request->validate([
            'category_id' => 'required|exists:station_categories,id',
            'code'        => [
                'required', 'string', 'max:30',
                // unique PAR station
                Rule::unique('station_products')->where(fn ($q) =>
                    $q->where('station_id', $stationId)
                ),
            ],
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        $lubricantProduct->update($request->only('category_id', 'name', 'code', 'price'));

        return redirect()->route('lubricant-products.index')->with('success', 'Lubrifiant modifié.');
    }

    public function destroy(StationProduct $lubricantProduct)
    {
        $lubricantProduct->delete();

        return redirect()->route('lubricant-products.index')->with('success', 'Lubrifiant supprimé.');
    }
}
