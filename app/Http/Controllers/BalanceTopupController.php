<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\BalanceTopup;
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
            'notes' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        $topup = BalanceTopup::create($data);

        // Incrémenter le solde
        $topup->client->increment('credit_balance', $data['amount']);
        return redirect()->route('balance-topups.index')->with('success', 'Recharge ajoutée.');
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
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $balanceTopup->amount;

        $balanceTopup->client->decrement('credit_balance', $oldAmount);
        $balanceTopup->client->increment('credit_balance', $data['amount']);

        $balanceTopup->update($data);
        return redirect()->route('balance-topups.index')->with('success', 'Recharge mise à jour.');
    }

    public function destroy(BalanceTopup $balanceTopup)
    {
        $balanceTopup->client->decrement('credit_balance', $balanceTopup->amount);
        $balanceTopup->delete();
        return back()->with('success', 'Recharge supprimée.');
    }
}

