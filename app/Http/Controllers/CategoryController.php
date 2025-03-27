<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StationCategory;
use App\Http\Requests\CategorieProduitRequest;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id'); // Assure-toi que ceci est bien stocké au moment de la sélection

        if (!$stationId) {
            return redirect()->route('station.selection')->with('error', 'Veuillez sélectionner une station.');
        }

        $data = StationCategory::where('station_id', $stationId)
            ->orderBy('name', 'asc') // Tri alphabétique
            ->paginate(10);

        return view('categories.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(CategorieProduitRequest $request)
    {
        $data = $request->validated();

        // Injecter la station depuis la session
        $data['station_id'] = session('selected_station_id');

        StationCategory::create($data);

        return redirect()->route('categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(StationCategory $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(CategorieProduitRequest $request, StationCategory $categorie)
    {
       // dd($request->validated());
        $categorie->update($request->validated());
        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(StationCategory $categorie)
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
