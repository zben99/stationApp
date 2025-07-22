<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientBalanceController extends Controller
{
    // Affiche le tableau de synthèse des avoirs par client
    public function index()
    {
        $stationId = session('selected_station_id');

        $clients = Client::with(['balanceTopups', 'balanceUsages'])
            ->where('station_id', $stationId)
            ->orderBy('name')
            ->get()
            ->filter(function ($client) {
                $totalRecharges = $client->balanceTopups->sum('amount');
                $totalUtilisation = $client->balanceUsages->sum('amount');

                return $totalRecharges > $totalUtilisation;
            });

        return view('clients.balances.summary', compact('clients'));
    }

    // Affiche le détail des avoirs d'un client spécifique
    public function show(Client $client)
    {
        $client->load(['balanceTopups', 'balanceUsages', 'station']);

        return view('clients.balances.show', compact('client'));
    }
}
