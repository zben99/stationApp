<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategorieProduitRequest;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        $data = Category::latest()->paginate(5);

        return view('categories.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(CategorieProduitRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès.');
    }

    public function edit(Category $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(CategorieProduitRequest $request, Category $categorie)
    {
       // dd($request->validated());
        $categorie->update($request->validated());
        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $categorie)
    {
        $categorie->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée.');
    }

    public function activerPourStation(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'categories' => 'required|array'
        ]);

        $station = Station::find($request->station_id);
        $station->categories()->sync($request->categories);

        return redirect()->back()->with('success', 'Catégories activées pour la station.');
    }
}
