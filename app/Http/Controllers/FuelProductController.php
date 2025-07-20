<?php

namespace App\Http\Controllers;

use App\Models\StationCategory;
use App\Models\StationProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FuelProductController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $categoryId = $request->input('category');

        $query = StationProduct::with('stationCategory', 'station')
            ->where('station_id', $stationId)
            ->whereHas('stationCategory', function ($q) {
                $q->whereIn('name', ['Carburant']);
            });

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->paginate(10);

        $categories = StationCategory::whereIn('name', ['Carburant'])
            ->where('station_id', $stationId)
            ->get();

        return view('fuel.products.index', compact('products', 'categories'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::whereIn('name', ['Carburant'])
            ->where('station_id', $stationId)
            ->get();

        return view('fuel.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $stationId = session('selected_station_id');

        /* ───── Validation ───── */
        $validated = $request->validate([
            'category_id' => ['required', 'exists:station_categories,id'],
            'code' => [
                'required', 'string', 'max:30',
                // unique PAR station
                Rule::unique('station_products')->where(fn ($q) => $q->where('station_id', $stationId)
                ),
            ],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        /* ───── Création ───── */
        StationProduct::create([
            'station_id' => $stationId,
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'price' => $validated['price'] ?? 0,
        ]);

        return redirect()
            ->route('lubricant-products.index')
            ->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(StationProduct $fuelProduct)
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::whereIn('name', ['Carburant'])
            ->where('station_id', $stationId)
            ->get();

        return view('fuel.products.edit', [
            'product' => $fuelProduct,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, StationProduct $fuelProduct)
    {
        $stationId = session('selected_station_id');

        $request->validate([
            'category_id' => 'required|exists:station_categories,id',
            'code' => [
                'required', 'string', 'max:30',
                Rule::unique('station_products')
                    ->where(fn ($q) => $q->where('station_id', $stationId))
                    ->ignore($fuelProduct->id),
            ],
            'name' => 'required|string|max:255',
            'prix_achat' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $fuelProduct->update($request->only('category_id', 'name', 'code', 'prix_achat', 'price'));

        return redirect()->route('fuel-products.index')->with('success', 'Carburant modifié.');
    }


    public function destroy(StationProduct $fuelProduct)
    {
        $fuelProduct->delete();

        return redirect()->route('fuel-products.index')->with('success', 'Carburant supprimé.');
    }
}
