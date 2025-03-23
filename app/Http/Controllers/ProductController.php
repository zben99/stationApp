<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Station;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'station')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $stations = Station::all();

        return view('products.create', compact('categories', 'stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'station_id' => 'required',
            'stock' => 'required|numeric',
            'price' => 'required|numeric'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Produit ajouté avec succès.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('type', 'fuel')->get();
        $stations = Station::all();
        return view('products.edit', compact('product', 'categories', 'stations'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'station_id' => 'required',
            'stock' => 'required|numeric',
            'price' => 'required|numeric'
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé avec succès.');
    }
}
