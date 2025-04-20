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
        $payments = CreditPayment::with(['client', 'creditTopup'])
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
        $creditTopups = CreditTopup::where('station_id', $stationId)->with('client')->get();

        return view('credit_payments.create', compact('clients', 'creditTopups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'credit_topup_id' => 'required|exists:credit_topups,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $creditTopup = CreditTopup::findOrFail($data['credit_topup_id']);

        // Validation métier
        if ($data['date'] < $creditTopup->date) {
            return back()->withErrors(['date' => 'La date du remboursement ne peut pas être antérieure à la date du crédit.'])->withInput();
        }

        $alreadyPaid = $creditTopup->payments()->sum('amount');
        $remaining = $creditTopup->amount - $alreadyPaid;

        if ($data['amount'] > $remaining) {
            return back()->withErrors(['amount' => 'Le montant du remboursement dépasse le solde restant du crédit.'])->withInput();
        }

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        $payment = CreditPayment::create($data);

        // Mise à jour du solde global client
        $payment->client->increment('credit_balance', $payment->amount);

        return redirect()->route('credit-payments.index')->with('success', 'Remboursement enregistré avec succès.');
    }

    public function edit(CreditPayment $creditPayment)
    {
        $stationId = session('selected_station_id');

        $clients = Client::where('station_id', $stationId)->get();
        $creditTopups = CreditTopup::where('station_id', $stationId)->with('client')->get();

        return view('credit_payments.edit', compact('creditPayment', 'clients', 'creditTopups'));
    }

    public function update(Request $request, CreditPayment $creditPayment)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'credit_topup_id' => 'required|exists:credit_topups,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $creditTopup = CreditTopup::findOrFail($data['credit_topup_id']);

        if ($data['date'] < $creditTopup->date) {
            return back()->withErrors(['date' => 'La date du remboursement ne peut pas être antérieure à la date du crédit.'])->withInput();
        }

        // Calcul du reste à rembourser (on enlève le montant actuel du remboursement pour pouvoir le mettre à jour)
        $alreadyPaid = $creditTopup->payments()->sum('amount') - $creditPayment->amount;
        $remaining = $creditTopup->amount - $alreadyPaid;

        if ($data['amount'] > $remaining) {
            return back()->withErrors(['amount' => 'Le montant du remboursement dépasse le solde restant du crédit.'])->withInput();
        }

        // Mise à jour du solde global
        $creditPayment->client->decrement('credit_balance', $creditPayment->amount);
        $creditPayment->client->increment('credit_balance', $data['amount']);

        $creditPayment->update($data);

        return redirect()->route('credit-payments.index')->with('success', 'Remboursement modifié avec succès.');
    }

    public function destroy(CreditPayment $creditPayment)
    {
        $creditPayment->client->decrement('credit_balance', $creditPayment->amount);
        $creditPayment->delete();

        return back()->with('success', 'Remboursement supprimé avec succès.');
    }
}
