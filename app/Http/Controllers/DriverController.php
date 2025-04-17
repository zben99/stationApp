<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::orderBy('name')->paginate(10);
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'permis' => 'nullable|string|max:100',
        ]);

        Driver::create($request->all());
        return redirect()->route('drivers.index')->with('success', 'Chauffeur ajouté avec succès.');
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'permis' => 'nullable|string|max:100',
        ]);

        $driver->update($request->all());
        return redirect()->route('drivers.index')->with('success', 'Chauffeur modifié avec succès.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Chauffeur supprimé avec succès.');
    }
}
