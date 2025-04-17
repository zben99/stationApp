<?php
namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;
use App\Models\StationProduct;
use App\Models\StationCategory;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        $products = StationProduct::with('stationCategory', 'station')
            ->where('station_id', $stationId)
            ->orderBy('name', 'asc') // Tri alphabétique par nom
            ->paginate(10);

        return view('products.index', compact('products'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }


    public function create()
    {
        $categories = StationCategory::all();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric'
        ]);

        // Injecter la station depuis la session
        $data['station_id'] = session('selected_station_id');

        StationProduct::create($data);

        return redirect()->route('products.index')->with('success', 'Produit ajouté avec succès.');
    }



    public function edit(StationProduct $product)
    {
        $categories = StationCategory::all(); // même logique que le create

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, StationProduct $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric'
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(StationProduct $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé avec succès.');
    }
}
