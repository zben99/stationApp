<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        if (!$stationId) {
            return redirect()->route('station.selection')->with('error', 'Veuillez sélectionner une station.');
        }

        $suppliers = Supplier::where('station_id', $stationId)
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('suppliers.index', compact('suppliers'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Fournisseur modifié avec succès.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}
