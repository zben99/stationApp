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

        $clients = Client::with('creditTopups', 'creditPayments')
            ->where('station_id', $stationId)
            ->get();

        return view('credit_topups.index', compact('clients'));
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
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();


        $topup = CreditTopup::create($data);


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
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $creditTopup->amount;


        $creditTopup->update($data);

        return redirect()->route('clients.topups', $creditTopup->client_id)->with('success', 'Recharge de crédit mise à jour.');
    }

    public function destroy(CreditTopup $creditTopup)
    {
        $creditTopup->delete();
        return back()->with('success', 'Recharge de crédit supprimée.');
    }

    public function show(Client $client)
    {
        $client->load(['creditTopups', 'creditPayments', 'station']);

        return view('credit_topups.show_client', compact('client'));
    }


}

