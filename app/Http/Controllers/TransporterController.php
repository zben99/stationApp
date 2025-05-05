<?php

namespace App\Http\Controllers;

use App\Models\Transporter;
use Illuminate\Http\Request;

class TransporterController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');

        $transporters = Transporter::where('station_id', $stationId)->orderBy('name')->paginate(10);

        return view('transporters.index', compact('transporters'));
    }

    public function create()
    {
        return view('transporters.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        // Injecter la station depuis la session
        $data['station_id'] = session('selected_station_id');

        Transporter::create($data);

        return redirect()->route('transporters.index')->with('success', 'Transporteur créé avec succès.');
    }

    public function edit(Transporter $transporter)
    {
        return view('transporters.edit', compact('transporter'));
    }

    public function update(Request $request, Transporter $transporter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $transporter->update($request->all());

        return redirect()->route('transporters.index')->with('success', 'Transporteur modifié avec succès.');
    }

    public function destroy(Transporter $transporter)
    {
        $transporter->delete();

        return redirect()->route('transporters.index')->with('success', 'Transporteur supprimé avec succès.');
    }
}
