<?php

namespace App\Exports;

use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseInvoicesExport implements FromCollection, WithHeadings
{
    protected $from;

    protected $to;

    protected $supplier;

    public function __construct($from = null, $to = null, $supplier = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->supplier = $supplier;
    }

    public function collection()
    {
        $stationId = Session::get('selected_station_id');

        $query = PurchaseInvoice::where('station_id', $stationId);

        if ($this->from) {
            $query->where('date', '>=', $this->from);
        }
        if ($this->to) {
            $query->where('date', '<=', $this->to);
        }
        if ($this->supplier) {
            $query->where('supplier_name', 'like', '%'.$this->supplier.'%');
        }

        $invoices = $query->select('date', 'invoice_number', 'supplier_name', 'amount_ht', 'amount_ttc')->get();

        $total_ht = $invoices->sum('amount_ht');
        $total_ttc = $invoices->sum('amount_ttc');

        $invoices->push([
            'date' => '',
            'invoice_number' => '',
            'supplier_name' => 'Total HT',
            'amount_ht' => $total_ht,
            'amount_ttc' => '',
        ]);

        $invoices->push([
            'date' => '',
            'invoice_number' => '',
            'supplier_name' => 'Total TTC',
            'amount_ht' => '',
            'amount_ttc' => $total_ttc,
        ]);

        return $invoices;

    }

    public function headings(): array
    {
        return ['Date', 'Numéro de facture', 'Fournisseur', 'Valeur HT', 'Valeur TTC'];
    }
}
