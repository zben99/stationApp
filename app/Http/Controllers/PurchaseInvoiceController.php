<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseInvoicesExport;
use App\Models\PurchaseInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $query = PurchaseInvoice::where('station_id', $stationId);

        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_name', 'like', '%'.$request->supplier.'%');
        }

        $invoices = $query->orderByDesc('date')->paginate(15)->appends($request->query());
        $i = ($invoices->currentPage() - 1) * $invoices->perPage();

        return view('purchase_invoices.index', compact('invoices', 'i'));
    }

    public function create()
    {
        return view('purchase_invoices.create');
    }

public function store(Request $request)
{
    // Validation des données de la facture
    $validated = $request->validate([
        'invoice_number' => 'required|string',
        'date' => 'required|date',
        'rotation' => 'required|string',
        'supplier_name' => 'required|string',
        'amount_ht' => 'required|numeric',
        'amount_ttc' => 'required|numeric',
    ]);

    // Création de la facture
    $invoice = new PurchaseInvoice();
    $invoice->invoice_number = $validated['invoice_number'];
    $invoice->date = $validated['date'];
    $invoice->rotation = $validated['rotation'];
    $invoice->supplier_name = $validated['supplier_name'];
    $invoice->amount_ht = $validated['amount_ht'];
    $invoice->amount_ttc = $validated['amount_ttc'];
    $invoice->station_id = session('selected_station_id');

    // Initialisation du montant payé et restant
    $invoice->amount_paid = 0;
    $invoice->amount_remaining = $validated['amount_ttc'];  // Le montant restant à payer est égal à amount_ttc initialement

    $invoice->created_by = auth()->id();;
    // Sauvegarde de la facture
    $invoice->save();

    return redirect()->route('purchase-invoices.index')->with('success', 'Facture créée avec succès');
}

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        return view('purchase_invoices.edit', compact('purchaseInvoice'));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'date' => 'required|date',
            'rotation' => 'nullable|string|in:6-14,14-22,22-6',
            'supplier_name' => 'required|string|max:255',
            'amount_ht' => 'required|numeric|min:0',
            'amount_ttc' => 'required|numeric|min:0',
        ]);

        $purchaseInvoice->update($data);

        return redirect()->route('purchase-invoices.index')->with('success', 'Facture mise à jour.');
    }

    public function exportPdf(Request $request)
    {
        $stationId = session('selected_station_id');
        $query = PurchaseInvoice::where('station_id', $stationId);

        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }
        if ($request->filled('supplier')) {
            $query->where('supplier_name', 'like', '%'.$request->supplier.'%');
        }

        $invoices = $query->orderBy('date')->get();

        $pdf = Pdf::loadView('exports.purchase_invoices_pdf', compact('invoices'));

        return $pdf->download('factures_achat_filtrées.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PurchaseInvoicesExport($request->from, $request->to, $request->supplier),
            'factures_achat_filtrées.xlsx'
        );
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->delete();

        return back()->with('success', 'Facture supprimée avec succès.');
    }


        /**
     * Afficher les paiements associés à une facture
     */
    public function showPayments($invoiceId)
    {
        $invoice = PurchaseInvoice::findOrFail($invoiceId);

        $payments = $invoice->payments; // Relation définie dans le modèle Invoice

        return view('purchase_invoices.show_payments', compact('invoice', 'payments'));
    }
}
