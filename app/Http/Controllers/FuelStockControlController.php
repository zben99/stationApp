<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FuelStockControl;

  use App\Exports\FuelStockControlExport;
use Maatwebsite\Excel\Facades\Excel;


class FuelStockControlController extends Controller
{

    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        $controls = FuelStockControl::with(['tank.product'])
            ->where('station_id', $stationId)
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('control_date', '>=', $request->from);
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('control_date', '<=', $request->to);
            })
            ->orderByDesc('control_date')
            ->paginate(20);

        return view('fuel_stock_controls.index', compact('controls'));
    }



public function export(Request $request)
{
    $stationId = session('selected_station_id');
    $from = $request->get('from');
    $to = $request->get('to');

    return Excel::download(
        new FuelStockControlExport($stationId, $from, $to),
        'controle_stock_snapshot_' . ($from ?? 'debut') . '_'. ($to ?? 'fin') . '.xlsx'
    );
}


}
