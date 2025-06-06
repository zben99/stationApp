<?php

namespace App\Http\Controllers;

use App\Models\Packaging;
use Illuminate\Http\Request;

class PackagingController extends Controller
{
    public function index(Request $request)
    {
         $stationId = session('selected_station_id');

        $query = Packaging::where('station_id', $stationId)
                ->orderBy('label');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $packagings = $query->get();

        return view('packagings.index', compact('packagings'));
    }

    public function create()
    {
        $types = ['lubrifiant', 'gaz', 'lavage', 'pea', 'autre'];
        $units = ['L', 'kg', 'u'];

        return view('packagings.create', compact('types', 'units'));
    }

    public function store(Request $request)
    {

         $data = $request->validate([
            'label' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|in:L,kg,u',
            'type' => 'nullable|string|max:50',
        ]);
        // Injecter la station depuis la session
        $data['station_id'] = session('selected_station_id');

        Packaging::create($data);

        return redirect()->route('packagings.index')->with('success', 'Conditionnement ajouté.');
    }

    public function edit(Packaging $packaging)
    {
        $types = ['lubrifiant', 'gaz', 'lavage', 'pea', 'autre'];
        $units = ['L', 'kg', 'u'];

        return view('packagings.edit', compact('packaging', 'types', 'units'));
    }

    public function update(Request $request, Packaging $packaging)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|in:L,kg,u',
            'type' => 'nullable|string|max:50',
        ]);

        $packaging->update($request->only('label', 'quantity', 'unit', 'type'));

        return redirect()->route('packagings.index')->with('success', 'Conditionnement modifié.');
    }

    public function destroy(Packaging $packaging)
    {
        $packaging->delete();

        return redirect()->route('packagings.index')->with('success', 'Conditionnement supprimé.');
    }
}
