<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuelIndex;
use App\Models\Pump;
use App\Models\User;
    use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\FuelReportExport;
use Maatwebsite\Excel\Facades\Excel;


class FuelReportController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $date = $request->input('date', date('Y-m-d'));
        $rotation = $request->input('rotation', '6-14');

        $fuelIndexes = FuelIndex::with(['pump.tank.product', 'user'])
            ->where('station_id', $stationId)
            ->where('date', $date)
            ->when($rotation, fn($q) => $q->where('rotation', $rotation))
            ->get();

        return view('reports.fuel', compact('fuelIndexes', 'date', 'rotation'));
    }


public function exportPdf(Request $request)
{
    $stationId = session('selected_station_id');
    $date = $request->input('date', date('Y-m-d'));
    $rotation = $request->input('rotation', '6-14');

    $fuelIndexes = FuelIndex::with(['pump.tank.product', 'user'])
        ->where('station_id', $stationId)
        ->where('date', $date)
        ->when($rotation, fn($q) => $q->where('rotation', $rotation))
        ->get();

    $pdf = Pdf::loadView('reports.pdf.fuel', compact('fuelIndexes', 'date', 'rotation'));
    return $pdf->download("rapport_ventes_fuel_{$date}_{$rotation}.pdf");
}

public function exportExcel(Request $request)
{
    $date = $request->input('date', date('Y-m-d'));
    $rotation = $request->input('rotation', '6-14');
    return Excel::download(new FuelReportExport($date, $rotation), "rapport_ventes_fuel_{$date}_{$rotation}.xlsx");
}

}
