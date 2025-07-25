<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditTopup;
use App\Traits\ValidatesRotation;
use Illuminate\Http\Request;

class CreditTopupController extends Controller
{
    use ValidatesRotation;
    public function index()
    {
        $stationId = session('selected_station_id');

        $clients = Client::with(['creditTopups', 'creditPayments'])
            ->where('station_id', $stationId)
            ->get()
            ->filter(function ($client) {
                $totalCredit = $client->creditTopups->sum('amount');
                $totalRembourse = $client->creditPayments->sum('amount');

                return $totalCredit > $totalRembourse;
            });

        return view('credit_topups.index', compact('clients'));
    }

    public function create()
    {
        $clients = Client::where('station_id', session('selected_station_id'))
            ->orderBy('name')
            ->get();

        return view('credit_topups.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'client_id' => ['nullable', 'string', 'max:255'],
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);

        $data['station_id'] = session('selected_station_id');
        $data['created_by'] = auth()->id();

        $data['client_id'] = $this->resolveEntityId(
            Client::class, $request->client_id, $data['station_id']
        );

        $topup = CreditTopup::create($data);

        return redirect()->route('credit-topups.index')->with('success', 'Crédit ajouté.');
    }

    private function resolveEntityId(string $model, ?string $value, int $stationId): ?int
    {
        if (empty($value)) {
            return null;
        }

        // Id numérique existant
        if (is_numeric($value)) {
            return $model::findOrFail((int) $value)->id;
        }

        // Nouveau nom : on cherche d’abord dans la même station
        $record = $model::firstOrCreate(
            ['station_id' => $stationId, 'name' => trim($value)]
        );

        return $record->id;
    }

    public function edit(CreditTopup $creditTopup)
    {
        $clients = Client::where('station_id', session('selected_station_id'))
            ->orderBy('name')
            ->get();

        return view('credit_topups.edit', compact('creditTopup', 'clients'));
    }

    public function update(Request $request, CreditTopup $creditTopup)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'notes' => 'nullable|string',
        ]);
        if (! $this->modificationAllowed(
            $creditTopup->station_id,
            $creditTopup->date,
            $creditTopup->rotation
        )) {
            return redirect()->route('clients.topups', $creditTopup->client_id)
                ->with('error', 'Cette rotation a déjà été validée. Modification impossible.');
        }        $creditTopup->update($data);
        return redirect()->route('clients.topups', $creditTopup->client_id)->with('success', 'Recharge de crédit mise à jour.');
    }

    public function destroy(CreditTopup $creditTopup)
    {
        if (! $this->modificationAllowed(
            $creditTopup->station_id,
            $creditTopup->date,
            $creditTopup->rotation
        )) {
            return back()->with('error', 'Cette rotation a déjà été validée. Suppression impossible.');
        }        $creditTopup->delete();
        return back()->with('success', 'Recharge de crédit supprimée.');
    }

    public function show(Client $client)
    {
        $client->load(['creditTopups', 'creditPayments', 'station']);

        return view('credit_topups.show_client', compact('client'));
    }
}
