<?php

namespace App\Http\Controllers;

use App\Exports\FuelStockExport;
use App\Models\Tank;
use App\Models\TankPhysicalStock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FuelStockController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $tanks = Tank::where('station_id', $stationId)->get();

        foreach ($tanks as $tank) {
            $tank->physical_stock_value = TankPhysicalStock::where('station_id', $stationId)
                ->where('tank_id', $tank->id)
                ->where('date', $request->to)
                ->value('quantity');
        }

        return view('fuel_stock_exports.index', [
            'tanks' => $tanks,
            'from' => $request->from,
            'to' => $request->to,
        ]);
    }

 public function exportExcel(Request $request)
{
    $request->validate([
    'from' => 'required|date',
    'to' => 'required|date|after_or_equal:from',
    'stocks' => 'array',
    'stocks.*' => 'nullable|numeric',
    ]);


    $stationId = session('selected_station_id');
    $from = $request->from;
    $to = $request->to;
    $force = $request->boolean('force', false); // 👈 conversion propre

    // 🔄 Enregistrement des stocks physiques
    if ($request->has('stocks')) {
        foreach ($request->get('stocks') as $tankId => $qty) {
            if ($qty === null || $qty === '') {
                continue;
            }

            TankPhysicalStock::updateOrCreate(
                [
                    'station_id' => $stationId,
                    'tank_id' => $tankId,
                    'date' => $to,
                ],
                ['quantity' => $qty]
            );
        }
    }

    // 📤 Export avec ou sans recalcul forcé
    return Excel::download(
        new FuelStockExport($from, $to, $stationId, $force),
        'controle_stock_fuel_'.$from.'_'.$to.'.xlsx'
    );
}

}
