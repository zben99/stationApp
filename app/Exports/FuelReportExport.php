<?php

namespace App\Exports;

use App\Models\FuelIndex;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FuelReportExport implements FromView
{
    protected $date;
    protected $rotation;

    public function __construct($date, $rotation)
    {
        $this->date = $date;
        $this->rotation = $rotation;
    }

    public function view(): View
    {
        $fuelIndexes = FuelIndex::with(['pump.tank.product', 'user'])
            ->where('station_id', session('selected_station_id'))
            ->where('date', $this->date)
            ->when($this->rotation, fn($q) => $q->where('rotation', $this->rotation))
            ->get();

        return view('reports.excel.fuel', [
            'fuelIndexes' => $fuelIndexes,
            'date' => $this->date,
            'rotation' => $this->rotation
        ]);
    }
}
