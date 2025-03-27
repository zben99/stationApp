<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        if (!$stationId) {
            return redirect()->route('station.selection')->with('error', 'Veuillez sélectionner une station.');
        }

        $clients = Client::where('station_id', $stationId)
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('clients.index', compact('clients'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:clients,phone',
            'email' => 'nullable|email|unique:clients,email',
            'address' => 'nullable|string',
            'credit_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $data['station_id'] = session('selected_station_id');

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }


    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }



    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:clients,phone,' . $client->id,
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'address' => 'nullable|string',
            'credit_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
