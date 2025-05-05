<?php

namespace App\Http\Controllers;

use App\Exports\ClientCreditExport;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        $clients = Client::where('station_id', $stationId)
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('clients.index', compact('clients'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:clients,phone',
            'email' => 'nullable|email|unique:clients,email',
            'address' => 'nullable|string',
            'credit_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:clients,phone,'.$client->id,
            'email' => 'nullable|email|unique:clients,email,'.$client->id,
            'address' => 'nullable|string',
            'credit_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    public function exportCreditHistoryPdf(Client $client)
    {
        $creditTopups = $client->creditTopups()->with('payments')->get();

        $pdf = Pdf::loadView('exports.client_credit_pdf', compact('client', 'creditTopups'));

        return $pdf->download('historique_credit_'.$client->name.'.pdf');
    }

    public function exportCreditHistoryExcel(Client $client)
    {
        return Excel::download(new ClientCreditExport($client), 'historique_credit_'.$client->name.'.xlsx');
    }
}
