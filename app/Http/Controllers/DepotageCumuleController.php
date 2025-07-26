<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FuelReceptionLine;
use Illuminate\Http\Request;
    use App\Exports\FuelDepotageCumuleExport;
use Maatwebsite\Excel\Facades\Excel;
    use Barryvdh\DomPDF\Facade\Pdf;


class DepotageCumuleController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        $start = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $end = $request->input('end_date') ?? now()->toDateString();
        $productId = $request->input('product_id');
        $tankId = $request->input('tank_id');
        $transporterId = $request->input('transporter_id');

        $lines = FuelReceptionLine::with([
                'tank.product',
                'reception.transporter'
            ])
            ->whereHas('reception', function ($q) use ($stationId, $start, $end, $transporterId) {
                $q->where('station_id', $stationId)
                  ->whereBetween('date_reception', [$start, $end]);

                if ($transporterId) {
                    $q->where('transporter_id', $transporterId);
                }
            })
            ->when($tankId, fn($q) => $q->where('tank_id', $tankId))
            ->when($productId, function ($q) use ($productId) {
                $q->whereHas('tank.product', fn($q) => $q->where('id', $productId));
            })
            ->get();

        // Groupement par produit/cuve
        $grouped = $lines->groupBy(function ($line) {
            return $line->tank->product->name . ' | ' . $line->tank->code;
        });

        return view('reports.depotage.cumule', compact('lines', 'grouped', 'start', 'end'));
    }



public function exportExcel(Request $request)
{
    $stationId = session('selected_station_id');
    $start = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
    $end = $request->input('end_date') ?? now()->toDateString();
    $productId = $request->input('product_id');

    $filename = 'depotage_cumule_' . $start . '_au_' . $end . '.xlsx';

    return Excel::download(new FuelDepotageCumuleExport($stationId, $start, $end, $productId), $filename);
}




public function exportPdf(Request $request)
{
    $stationId = session('selected_station_id');
    $start = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
    $end = $request->input('end_date') ?? now()->toDateString();
    $productId = $request->input('product_id');

    $lines = FuelReceptionLine::with(['tank.product', 'reception'])
        ->whereHas('reception', function ($q) use ($stationId, $start, $end) {
            $q->where('station_id', $stationId)
              ->whereBetween('date_reception', [$start, $end]);
        })
        ->when($productId, function ($q) use ($productId) {
            $q->whereHas('tank.product', function ($q) use ($productId) {
                $q->where('id', $productId);
            });
        })
        ->get();

    $grouped = $lines->groupBy(function ($line) {
        return $line->tank->product->name . ' | ' . $line->tank->code;
    });

    $pdf = Pdf::loadView('reports.depotage.pdf.cumule', compact('grouped', 'start', 'end'))
              ->setPaper('a4', 'landscape');

    $fileName = 'depotage_cumule_' . $start . '_au_' . $end . '.pdf';

    return $pdf->download($fileName);
}

}
