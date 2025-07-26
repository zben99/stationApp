<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
    use App\Exports\ClientCreditReportExport;
use Maatwebsite\Excel\Facades\Excel;

use Barryvdh\DomPDF\Facade\Pdf;

class ClientCreditReportController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');
        $clientId = $request->input('client_id');

        $clientsQuery = Client::with(['creditTopups', 'creditPayments'])
            ->where('station_id', $stationId);

        if ($clientId) {
            $clientsQuery->where('id', $clientId);
        }

        $clients = $clientsQuery->get()->map(function ($client) {
            $totalCredit = $client->creditTopups->sum('amount');
            $totalRepayment = $client->creditPayments->sum('amount');
            $solde = $totalCredit - $totalRepayment;

            return (object) [
                'name' => $client->name,
                'phone' => $client->phone,
                'credit' => $totalCredit,
                'repayment' => $totalRepayment,
                'balance' => $solde,
            ];
        });

        return view('reports.clients.credits', compact('clients', 'clientId'));
    }



public function exportExcel(Request $request)
{
    $stationId = session('selected_station_id');
    $clientId = $request->input('client_id');

    $fileName = 'credits_clients' . ($clientId ? "_client_$clientId" : '') . '.xlsx';

    return Excel::download(new ClientCreditReportExport($stationId, $clientId), $fileName);
}



public function exportPdf(Request $request)
{
    $stationId = session('selected_station_id');
    $clientId = $request->input('client_id');

    $clientsQuery = Client::with(['creditTopups', 'creditPayments'])
        ->where('station_id', $stationId);

    if ($clientId) {
        $clientsQuery->where('id', $clientId);
    }

    $clients = $clientsQuery->get()->map(function ($client) {
        $totalCredit = $client->creditTopups->sum('amount');
        $totalRepayment = $client->creditPayments->sum('amount');
        $solde = $totalCredit - $totalRepayment;

        return (object)[
            'name' => $client->name,
            'phone' => $client->phone,
            'credit' => $totalCredit,
            'repayment' => $totalRepayment,
            'balance' => $solde,
        ];
    });

    $pdf = Pdf::loadView('reports.clients.pdf.credits', compact('clients'))->setPaper('a4', 'portrait');

    $fileName = 'credits_clients' . ($clientId ? "_client_$clientId" : '') . '.pdf';

    return $pdf->download($fileName);
}

}
