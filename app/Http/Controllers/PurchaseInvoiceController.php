<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PurchaseInvoicesExport;
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
            $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
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
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'date' => 'required|date',
            'rotation' => 'nullable|string|in:6-14,14-22,22-6',
            'supplier_name' => 'required|string|max:255',
            'amount_ht' => 'required|numeric|min:0',
            'amount_ttc' => 'required|numeric|min:0',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        PurchaseInvoice::create($data);

        return redirect()->route('purchase-invoices.index')->with('success', 'Facture enregistrée avec succès.');
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

        if ($request->filled('from')) $query->where('date', '>=', $request->from);
        if ($request->filled('to')) $query->where('date', '<=', $request->to);
        if ($request->filled('supplier')) $query->where('supplier_name', 'like', '%' . $request->supplier . '%');

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
}
