<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Afficher le formulaire de paiement pour une facture spécifique
     */
    public function create($invoiceId)
    {
        $invoice = PurchaseInvoice::findOrFail($invoiceId);
        return view('payments.create', compact('invoice'));
    }



    // Dans PaymentController.php

    public function store(Request $request, $invoiceId)
    {
        // Validation des données
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
             'rotation' => 'required|in:6-14,14-22,22-6', // Rotation obligatoire et doit correspondre à l'une des valeurs
        ]);

        // Trouver la facture associée
        $invoice = PurchaseInvoice::findOrFail($invoiceId);

        // Vérifier si le paiement dépasse le montant restant dû
        if ($validated['amount'] > $invoice->amount_remaining) {
            return redirect()->back()->withErrors(['amount' => 'Le paiement ne peut pas dépasser le montant restant dû']);
        }

        // Créer un enregistrement de paiement
        $payment = new Payment();
        $payment->invoice_id = $invoice->id;
        $payment->amount = $validated['amount'];
        $payment->payment_date = $validated['payment_date'];
        $payment->rotation = $validated['rotation']; // Ajout de la rotation
        $payment->save();

        // Mettre à jour les montants de la facture
        $invoice->updateAmounts();  // Met à jour le montant payé et le montant restant

        return redirect()->route('invoices.showPayments', $invoice->id)
                        ->with('success', 'Paiement enregistré avec succès');
    }


    public function destroy($paymentId)
    {
        // Trouver le paiement à supprimer
        $payment = Payment::findOrFail($paymentId);

        // Récupérer l'id de la facture associée avant de supprimer le paiement
        $invoiceId = $payment->invoice_id;

        // Supprimer le paiement
        $payment->delete();

        // Mettre à jour les montants dans la facture
        $payment->invoice->updateAmounts();  // Met à jour les montants de la facture après suppression

        return redirect()->route('invoices.showPayments', $invoiceId)
                        ->with('success', 'Le paiement a été supprimé avec succès');
    }


}
