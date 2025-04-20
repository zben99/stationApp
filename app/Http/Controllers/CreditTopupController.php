<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditTopup;
use Illuminate\Http\Request;

class CreditTopupController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $topups = CreditTopup::with('client')
            ->where('station_id', $stationId)
            ->latest()
            ->paginate(10);

        return view('credit_topups.index', compact('topups'));
    }

    public function create()
    {
        $clients = Client::where('station_id', session('selected_station_id'))->get();
        return view('credit_topups.create', compact('clients'));
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


        $topup = CreditTopup::create($data);

        // Décrémenter le solde (augmente la dette)
        $topup->client->decrement('credit_balance', $data['amount']);


        return redirect()->route('credit-topups.index')->with('success', 'Crédit ajouté.');
    }

    public function edit(CreditTopup $creditTopup)
    {
        $clients = Client::where('station_id', session('selected_station_id'))->get();
        return view('credit_topups.edit', compact('creditTopup', 'clients'));
    }

    public function update(Request $request, CreditTopup $creditTopup)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $creditTopup->amount;

        $creditTopup->client->increment('credit_balance', $oldAmount); // annule l'ancien
        $creditTopup->client->decrement('credit_balance', $data['amount']); // applique le nouveau

        $creditTopup->update($data);

        return redirect()->route('credit-topups.index')->with('success', 'Recharge de crédit mise à jour.');
    }

    public function destroy(CreditTopup $creditTopup)
    {
        $creditTopup->client->increment('credit_balance', $creditTopup->amount);
        $creditTopup->delete();
        return back()->with('success', 'Recharge de crédit supprimée.');
    }
}

