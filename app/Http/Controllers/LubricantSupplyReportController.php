<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LubricantReceptionBatch;
use Illuminate\Http\Request;

class LubricantSupplyReportController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $start = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();

        $batches = LubricantReceptionBatch::with('supplier', 'details.productPackaging.product')
            ->where('station_id', $stationId)
            ->whereBetween('date', [$start, $end])
            ->orderByDesc('date')
            ->get();

        return view('reports.supplies.lubricants', compact('batches', 'start', 'end'));
    }
}
