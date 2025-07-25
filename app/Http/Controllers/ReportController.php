<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
   use App\Models\DailyRevenueValidation;
use App\Exports\ConsolidatedPeriodExport;
use App\Pdf\ConsolidatedPeriodPdf;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
     public function index()
    {
        return view('reports.index');
    }




public function showConsolidatedPeriod(Request $request)
{
    $stationId = session('selected_station_id');
    $start = $request->start_date;
    $end = $request->end_date;

    $validations = [];

    if ($start && $end) {
        $validations = DailyRevenueValidation::where('station_id', $stationId)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderByRaw("FIELD(rotation, '6-14', '14-22', '22-6')")
            ->get();
    }

    return view('reports.recette_consolidee_periode', compact('start', 'end', 'validations'));
}

public function exportConsolidatedPeriodExcel(Request $request)
{
    $stationId = session('selected_station_id');
    $start = $request->start_date;
    $end = $request->end_date;

    return Excel::download(
        new ConsolidatedPeriodExport($stationId, $start, $end),
        "recettes_consolidees_{$start}_{$end}.xlsx"
    );
}

public function exportConsolidatedPeriodPdf(Request $request)
{
    $stationId = session('selected_station_id');
    $start = $request->start_date;
    $end = $request->end_date;

    $validations = DailyRevenueValidation::where('station_id', $stationId)
        ->whereBetween('date', [$start, $end])
        ->orderBy('date')
        ->orderByRaw("FIELD(rotation, '6-14', '14-22', '22-6')")
        ->get();

    $pdf = Pdf::loadView('reports.pdf.recette_consolidee_periode', compact('validations', 'start', 'end'));
    return $pdf->download("recettes_consolidees_{$start}_{$end}.pdf");
}

}
