<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyRevenueValidation;

class DailyRevenueReportController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $date = $request->input('date', date('Y-m-d'));
        $rotation = $request->input('rotation', '6-14');

        $validation = DailyRevenueValidation::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->first();

        return view('reports.daily-revenue', compact('validation', 'date', 'rotation'));
    }
}
