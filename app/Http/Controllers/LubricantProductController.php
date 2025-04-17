<?php

namespace App\Http\Controllers;

use App\Models\StationProduct;
use App\Models\StationCategory;
use Illuminate\Http\Request;

class LubricantProductController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');


        $products = StationProduct::with('stationCategory', 'station')
            ->where('station_id', $stationId)
            ->whereHas('stationCategory', fn($q) => $q->where('type', 'lubrifiant'))
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('lubricants.products.index', compact('products'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::where('station_id', $stationId)
            ->where('type', 'lubrifiant')
            ->get();

        return view('lubricants.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $stationId = session('selected_station_id');

        $request->validate([
            'category_id' => 'required|exists:station_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        StationProduct::create([
            'station_id' => $stationId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('lubricant-products.index')->with('success', 'Lubrifiant ajouté.');
    }

    public function edit(StationProduct $lubricantProduct)
    {
        $stationId = session('selected_station_id');

        $categories = StationCategory::where('station_id', $stationId)
            ->where('type', 'lubrifiant')
            ->get();

        return view('lubricants.products.edit', [
            'product' => $lubricantProduct,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, StationProduct $lubricantProduct)
    {
        $request->validate([
            'category_id' => 'required|exists:station_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        $lubricantProduct->update($request->only('category_id', 'name', 'price'));

        return redirect()->route('lubricant-products.index')->with('success', 'Lubrifiant modifié.');
    }

    public function destroy(StationProduct $lubricantProduct)
    {
        $lubricantProduct->delete();
        return redirect()->route('lubricant-products.index')->with('success', 'Lubrifiant supprimé.');
    }
}
