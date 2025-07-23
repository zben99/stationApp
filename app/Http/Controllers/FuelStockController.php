<?php

namespace App\Http\Controllers;

use App\Exports\FuelStockExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FuelStockController extends Controller
{
    public function index()
    {
        return view('fuel_stock_exports.index');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $stationId = session('selected_station_id');

        return Excel::download(
            new FuelStockExport($request->from, $request->to, $stationId),
            'controle_stock_fuel_'.$request->from.'_'.$request->to.'.xlsx'
        );
    }
}
