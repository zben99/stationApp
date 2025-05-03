<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditPayment;
use App\Models\CreditTopup;
use Illuminate\Http\Request;

class CreditPaymentController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $payments = CreditPayment::with(['client'])
            ->where('station_id', $stationId)
            ->latest()
            ->paginate(10);

        $i = ($payments->currentPage() - 1) * $payments->perPage();

        return view('credit_payments.index', compact('payments', 'i'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');

        $clients = Client::where('station_id', $stationId)->get();

        return view('credit_payments.create', compact('clients'));
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

        $client = Client::with('creditTopups', 'creditPayments')->findOrFail($data['client_id']);

        $totalCredit = $client->creditTopups->sum('amount');
        $totalRembourse = $client->creditPayments->sum('amount');
        $reste = $totalCredit - $totalRembourse;

        if ($data['amount'] > $reste) {
            return back()->withErrors(['amount' => 'Le montant du remboursement dépasse le crédit restant du client.'])->withInput();
        }

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        CreditPayment::create($data);

        return redirect()->route('credit-topups.index')->with('success', 'Remboursement enregistré avec succès.');
    }


    public function edit(CreditPayment $creditPayment)
    {
        $stationId = session('selected_station_id');

        $clients = Client::where('station_id', $stationId)->get();

        return view('credit_payments.edit', compact('creditPayment', 'clients'));
    }

    public function update(Request $request, CreditPayment $creditPayment)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $client = Client::with('creditTopups', 'creditPayments')->findOrFail($data['client_id']);

        // Calcul du solde disponible en soustrayant l'ancien remboursement
        $totalCredit = $client->creditTopups->sum('amount');
        $totalRembourse = $client->creditPayments->sum('amount') - $creditPayment->amount;
        $reste = $totalCredit - $totalRembourse;

        if ($data['amount'] > $reste) {
            return back()->withErrors(['amount' => 'Le montant du remboursement dépasse le solde total disponible du client.'])->withInput();
        }

        $creditPayment->update($data);

        return redirect()->route('clients.payments', $creditPayment->client_id)->with('success', 'Remboursement modifié avec succès.');
    }


    public function destroy(CreditPayment $creditPayment)
    {
        $creditPayment->client->decrement('credit_balance', $creditPayment->amount);
        $creditPayment->delete();

        return back()->with('success', 'Remboursement supprimé avec succès.');
    }
}
