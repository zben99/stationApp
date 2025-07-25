<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FuelReception;
use Illuminate\Http\Request;
  use App\Exports\FuelSupplyExport;
use Maatwebsite\Excel\Facades\Excel;
 use Barryvdh\DomPDF\Facade\Pdf;


class FuelSupplyReportController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();

        $receptions = FuelReception::with(['lines.tank.product', 'transporter'])
            ->where('station_id', $stationId)
            ->whereBetween('date_reception', [$start, $end])
            ->orderByDesc('date_reception')
            ->get();

        return view('reports.supplies.fuel', compact('receptions', 'start', 'end'));
    }



    public function exportExcel(Request $request)
    {
        $stationId = session('selected_station_id');
        $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();

       $fileName = 'approvisionnement_carburant_' . $start . '_au_' . $end . '.xlsx';
return Excel::download(new FuelSupplyExport($stationId, $start, $end), $fileName);

    }




public function exportPdf(Request $request)
{
    $stationId = session('selected_station_id');
    $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
    $end = $request->input('end_date') ?? now()->toDateString();

    $receptions = FuelReception::with(['lines.tank.product', 'transporter'])
        ->where('station_id', $stationId)
        ->whereBetween('date_reception', [$start, $end])
        ->orderByDesc('date_reception')
        ->get();


    $pdf = Pdf::loadView('reports.supplies.pdf.fuel', compact('receptions', 'start', 'end'))
          ->setPaper('a4', 'landscape');

    $fileName = 'approvisionnement_carburant_' . $start . '_au_' . $end . '.pdf';
return $pdf->download($fileName);

}

}
