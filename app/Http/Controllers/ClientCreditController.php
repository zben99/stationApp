<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditPayment;
use App\Models\CreditTopup;
use Illuminate\Http\Request;

class ClientCreditController extends Controller
{
    // Affiche la fiche client complète : crédits, remboursements, solde
    public function show(Client $client)
    {
        $client->load(['station', 'creditTopups', 'creditPayments']);

        return view('clients.credit_summary', compact('client'));
    }

    // Liste des crédits d'un client
    public function topups(Client $client)
    {
        $topups = CreditTopup::where('client_id', $client->id)->latest()->paginate(10);

        return view('clients.topups.index', compact('client', 'topups'));
    }

    // Liste des remboursements d'un client
    public function payments(Client $client)
    {
        $payments = CreditPayment::where('client_id', $client->id)->latest()->paginate(10);

        return view('clients.payments.index', compact('client', 'payments'));
    }

    // Modifier un crédit
    public function editTopup(CreditTopup $topup)
    {
        return view('clients.topups.edit', compact('topup'));
    }

    public function updateTopup(Request $request, CreditTopup $topup)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $topup->update($data);

        return redirect()->route('clients.topups', $topup->client_id)->with('success', 'Crédit mis à jour.');
    }

    // Modifier un remboursement
    public function editPayment(CreditPayment $payment)
    {
        return view('clients.payments.edit', compact('payment'));
    }

    public function updatePayment(Request $request, CreditPayment $payment)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update($data);

        return redirect()->route('clients.payments', $payment->client_id)->with('success', 'Remboursement mis à jour.');
    }

    // Suppression
    public function destroyTopup(CreditTopup $topup)
    {
        $topup->delete();

        return back()->with('success', 'Crédit supprimé.');
    }

    public function destroyPayment(CreditPayment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Remboursement supprimé.');
    }
}
