<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\Supplier;
use App\Models\TankStock;
use Illuminate\Http\Request;
use App\Models\FuelReception;

class FuelReceptionController extends Controller
{
    public function index()
    {
        $receptions = FuelReception::with('tank')->latest()->get();
        return view('fuel_receptions.index', compact('receptions'));
    }

    public function create()
    {
        $tanks = Tank::with('product')->get();
        $suppliers = Supplier::all();
        return view('fuel_receptions.create', compact('tanks', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'date_reception' => 'required|date',
            'quantite_livree' => 'required|numeric|min:0',
            'densite' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'num_bl' => 'nullable|string|max:100',
            'remarques' => 'nullable|string',
        ]);

        $reception = FuelReception::create($request->all());

        $stock = TankStock::firstOrCreate(['tank_id' => $request->tank_id]);
        $stock->quantite_actuelle += $request->quantite_livree;
        $stock->save();

        return redirect()->route('fuel-receptions.index')->with('success', 'Réception enregistrée.');
    }

    public function edit(FuelReception $fuelReception)
    {
        $tanks = Tank::with('product')->get();
        $suppliers = Supplier::all();
        return view('fuel_receptions.edit', [
            'fuelReception' => $fuelReception,
            'tanks' => $tanks,
            'suppliers'=> $suppliers
        ]);

    }

    public function update(Request $request, FuelReception $fuelReception)
    {
        $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'date_reception' => 'required|date',
            'quantite_livree' => 'required|numeric|min:0',
            'densite' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'num_bl' => 'nullable|string|max:100',
            'remarques' => 'nullable|string',
        ]);

        // Ajuster le stock
        $old_quantity = $fuelReception->quantite_livree;
        $fuelReception->update($request->all());

        $stock = TankStock::firstOrCreate(['tank_id' => $request->tank_id]);
        $stock->quantite_actuelle += ($request->quantite_livree - $old_quantity);
        $stock->save();

        return redirect()->route('fuel-receptions.index')->with('success', 'Réception modifiée.');
    }

    public function destroy(FuelReception $fuelReception)
    {
        $stock = TankStock::where('tank_id', $fuelReception->tank_id)->first();
        if ($stock) {
            $stock->quantite_actuelle -= $fuelReception->quantite_livree;
            $stock->save();
        }

        $fuelReception->delete();
        return redirect()->route('fuel-receptions.index')->with('success', 'Réception supprimée.');
    }
}
