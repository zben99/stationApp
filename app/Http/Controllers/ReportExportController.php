<?php

namespace App\Http\Controllers;

use App\Models\DailyRevenueValidation;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\ConsolidatedRevenueExport;


class ReportExportController extends Controller
{
    public function exportConsolideePdf(Request $request)
    {
        $stationId = session('selected_station_id');
        $date = $request->input('date');
        $rotation = $request->input('rotation');

        $validation = DailyRevenueValidation::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->firstOrFail();

        $pdf = Pdf::loadView('reports.pdf.recette_consolidee', compact('validation', 'date', 'rotation'));

        return $pdf->download("recette_consolidee_{$date}_{$rotation}.pdf");
    }



public function exportExcel(Request $request)
{
    $stationId = session('selected_station_id');
    $date = $request->date;
    $rotation = $request->rotation;

    $validation = \App\Models\DailyRevenueValidation::where('station_id', $stationId)
        ->where('date', $date)
        ->where('rotation', $rotation)
        ->first();

    if (!$validation) {
        return back()->with('error', 'Aucune donnée trouvée.');
    }

    $export = new ConsolidatedRevenueExport($validation, $date, $rotation);
    $filename = "recette_consolidee_{$date}_{$rotation}.xlsx";

    return Excel::download($export, $filename);
}


}

