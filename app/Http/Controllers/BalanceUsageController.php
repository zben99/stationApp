<?php

namespace App\Http\Controllers;

use App\Models\BalanceUsage;
use App\Models\Client;
use Illuminate\Http\Request;

class BalanceUsageController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $usages = BalanceUsage::with('client')
            ->where('station_id', $stationId)
            ->latest()
            ->paginate(10);

        return view('balance_usages.index', compact('usages'));
    }

    public function create()
    {
        $clients = Client::where('station_id', session('selected_station_id'))->get();
        return view('balance_usages.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        BalanceUsage::create($data);

        return redirect()->route('balances.summary')->with('success', 'Avoir servi enregistré avec succès.');
    }

    public function edit(BalanceUsage $balanceUsage)
    {
        $clients = Client::where('station_id', session('selected_station_id'))->get();
        return view('balance_usages.edit', compact('balanceUsage', 'clients'));
    }

    public function update(Request $request, BalanceUsage $balanceUsage)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $balanceUsage->update($data);

        return redirect()->route('balance-usages.index')->with('success', 'Avoir servi mis à jour avec succès.');
    }

    public function destroy(BalanceUsage $balanceUsage)
    {
        $balanceUsage->delete();

        return back()->with('success', 'Avoir servi supprimé avec succès.');
    }

    public function byClient(Client $client)
    {
        $stationId = session('selected_station_id');

        // Vérifie que le client appartient bien à la station active
        if ($client->station_id != $stationId) {
            abort(403, 'Accès non autorisé à ce client.');
        }

        // Récupère les avoirs servis de ce client
        $usages = $client->balanceUsages()
            ->orderByDesc('date')
            ->get();

        return view('balance_usages.client_index', compact('client', 'usages'));
    }

}
