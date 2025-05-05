<?php

namespace App\Http\Controllers;

use App\Models\BalanceTopup;
use App\Models\Client;
use Illuminate\Http\Request;

class BalanceTopupController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $topups = BalanceTopup::with('client')
            ->where('station_id', $stationId)
            ->latest()
            ->paginate(10);

        return view('balance_topups.index', compact('topups'));
    }

    public function create()
    {
        $clients = Client::where('station_id', session('selected_station_id'))->get();

        return view('balance_topups.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        $topup = BalanceTopup::create($data);

        return redirect()->route('balances.summary')->with('success', 'Recharge ajoutée.');
    }

    public function edit(BalanceTopup $balanceTopup)
    {
        $clients = Client::where('station_id', session('selected_station_id'))->get();

        return view('balance_topups.edit', compact('balanceTopup', 'clients'));
    }

    public function update(Request $request, BalanceTopup $balanceTopup)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $balanceTopup->amount;

        $balanceTopup->update($data);

        return redirect()->route('clients.balance.topups', $balanceTopup->client_id)->with('success', 'Recharge mise à jour.');
    }

    public function destroy(BalanceTopup $balanceTopup)
    {
        $balanceTopup->delete();

        return back()->with('success', 'Recharge supprimée.');
    }

    public function byClient(Client $client)
    {
        $stationId = session('selected_station_id');

        // Sécurité : s'assurer que le client appartient à la station active
        if ($client->station_id != $stationId) {
            abort(403, 'Accès non autorisé à ce client.');
        }

        // Charger ses recharges de solde (avoir perçu)
        $topups = $client->balanceTopups()
            ->orderByDesc('date')
            ->get();

        return view('balance_topups.client_index', compact('client', 'topups'));
    }
}
