<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LubricantReceptionBatch;
use Illuminate\Http\Request;
use App\Exports\LubricantSupplyExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;



class LubricantSupplyReportController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();

        $batches = LubricantReceptionBatch::with('supplier', 'receptions.packaging.product', 'receptions.packaging.packaging')
        ->where('station_id', $stationId)
        ->whereBetween('date_reception', [$start, $end]) // ← ici
        ->orderByDesc('date_reception')
        ->get();


        return view('reports.supplies.lubricants', compact('batches', 'start', 'end'));
    }



    public function exportExcel(Request $request)
    {
        $stationId = session('selected_station_id');
        $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();

        $fileName = 'approvisionnement_lubrifiants_' . $start . '_au_' . $end . '.xlsx';
        return Excel::download(new LubricantSupplyExport($stationId, $start, $end), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $stationId = session('selected_station_id');
        $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();



        $batches = LubricantReceptionBatch::with('supplier', 'receptions.packaging.product', 'receptions.packaging.packaging')
        ->where('station_id', $stationId)
        ->whereBetween('date_reception', [$start, $end]) // ← ici
        ->orderByDesc('date_reception')
        ->get();

        $pdf = Pdf::loadView('reports.supplies.pdf.lubricants', compact('batches', 'start', 'end'))
                ->setPaper('a4', 'landscape');

        $fileName = 'approvisionnement_lubrifiants_' . $start . '_au_' . $end . '.pdf';
        return $pdf->download($fileName);
    }


}
