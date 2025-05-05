<?php

namespace App\Exports;

use App\Models\FuelReception;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FuelReceptionExport implements FromView
{
    protected $reception;

    public function __construct(FuelReception $reception)
    {
        $this->reception = $reception;
    }

    public function view(): View
    {
        $this->reception->load('station', 'transporter', 'driver', 'lines.tank');

        return view('exports.reception', [
            'reception' => $this->reception,
        ]);
    }
}
