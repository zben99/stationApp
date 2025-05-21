<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Tank;
use App\Models\Client;
use App\Models\Driver;
use App\Models\TankStock;
use App\Models\CreditTopup;
use App\Models\Transporter;
use Illuminate\Http\Request;
use App\Models\FuelReception;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\FuelReceptionLine;
use App\Exports\FuelReceptionExport;
use Maatwebsite\Excel\Facades\Excel;

class FuelReceptionController extends Controller
{
    public function index(Request $request)
    {
        $stationId = session('selected_station_id');

        $query = FuelReception::with(['station', 'transporter', 'driver', 'lines'])
            ->where('station_id', $stationId)
            ->orderBy('date_reception', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('date_reception', $request->date);
        }

        if ($request->filled('rotation')) {
            $query->where('rotation', $request->rotation);
        }

        $receptions = $query->paginate(10)->appends($request->all());

        return view('fuel_receptions.index', compact('receptions'));
    }

    public function create()
    {

        $stationId = session('selected_station_id');

        $tanks = Tank::where('station_id', $stationId)->get();
        $transporters = Transporter::where('station_id', $stationId)->get();
        $drivers = Driver::where('station_id', $stationId)->get();

        return view('fuel_receptions.create', compact('tanks', 'transporters', 'drivers'));
    }




public function store(Request $request)
{
    /* 1° VALIDATION ---------------------------------------------------- */
    $data = $request->validate([
        'date_reception'                 => ['required','date'],
        'rotation'                       => ['required','in:6-14,14-22,22-6'],
        'num_bl'                         => ['nullable','string','max:255'],
        'transporter_id'                 => ['required','string','max:255'],
        'driver_id'                      => ['required','string','max:255'],
        'vehicle_registration'           => ['nullable','string','max:30'],
        'remarques'                      => ['nullable','string'],

        'tanks.*.tank_id'                => ['required','exists:tanks,id'],
        'tanks.*.jauge_avant'            => ['nullable','numeric'],
        'tanks.*.reception_par_cuve'     => ['nullable','numeric'],
        'tanks.*.jauge_apres'            => ['nullable','numeric'],
        'tanks.*.contre_plein_litre'     => ['nullable','numeric','min:0'],
    ]);

    /* 2° CONTRÔLE D’UNICITÉ DES CUVES --------------------------------- */
    $tankIds = array_column($data['tanks'], 'tank_id');
    if (count($tankIds) !== count(array_unique($tankIds))) {
        return back()->withErrors(['error' => 'Une même cuve ne peut pas apparaître plusieurs fois.'])
                     ->withInput();
    }

    /* 3° STATION + RÉSOLUTION TRANSPORTEUR / CHAUFFEUR ---------------- */
    $stationId = session('selected_station_id');
    if (!$stationId) {
        return back()->withErrors(['error' => 'Aucune station sélectionnée.'])->withInput();
    }

    $data['station_id'] = $stationId;
    $data['transporter_id'] = $this->resolveEntityId(
        Transporter::class, $data['transporter_id'], $stationId
    );
    $data['driver_id'] = $this->resolveEntityId(
        Driver::class, $data['driver_id'], $stationId
    );

    /* 4° ENREGISTREMENT – TRANSACTION --------------------------------- */
    DB::beginTransaction();

    try {
        /* 4.1 Entête de réception */
        $reception = FuelReception::create([
            'station_id'           => $stationId,
            'date_reception'       => $data['date_reception'],
            'rotation'             => $data['rotation'],
            'num_bl'               => $data['num_bl']           ?? null,
            'transporter_id'       => $data['transporter_id'],
            'driver_id'            => $data['driver_id'],
            'vehicle_registration' => $data['vehicle_registration'] ?? null,
            'remarques'            => $data['remarques']        ?? null,
        ]);

        $totalContreValeur = 0;

        /* 4.2 Parcours des cuves */
        foreach ($data['tanks'] as $line) {
            $tank        = Tank::findOrFail($line['tank_id']);
            $unitPrice   = $tank->product->price;
            $stockActuel = $tank->stock->quantite_actuelle ?? 0;

            $jaugeAvant   = $line['jauge_avant']        ?? 0;
            $jaugeApres   = $line['jauge_apres']        ?? 0;
            $qteReception = $line['reception_par_cuve'] ?? 0;
            $contreLitres = $line['contre_plein_litre'] ?? 0;
            $contreValeur = $contreLitres * $unitPrice;

            /* sécurité capacité */
            $qteProjetee = $stockActuel + $qteReception - $contreLitres;
            if ($qteProjetee > $tank->capacite) {
                throw new \Exception("Dépassement de capacité sur la cuve {$tank->code}.");
            }

            FuelReceptionLine::create([
                'fuel_reception_id'   => $reception->id,
                'tank_id'             => $tank->id,
                'jauge_avant'         => $jaugeAvant ?: null,
                'reception_par_cuve'  => $qteReception,
                'contre_plein_litre'  => $contreLitres,
                'contre_plein_valeur' => $contreValeur,
                'jauge_apres'         => $jaugeApres ?: null,
                'ecart_reception'     => ($jaugeApres - $jaugeAvant) - ($qteReception - $contreLitres),
                'ecart_stock'         => $jaugeAvant - $stockActuel,
            ]);

            /* mise à jour stock cuve */
            TankStock::updateOrCreate(
                ['tank_id' => $tank->id],
                ['quantite_actuelle' => $qteProjetee]
            );

            $totalContreValeur += $contreValeur;
        }

        /* 4.3 Créance contre-plein → Client virtuel “transporteur” */
        if ($totalContreValeur > 0) {
            // Transporteur
            $transporter = Transporter::find($data['transporter_id']);

            // Client correspondant (créé si absent)
            $client = Client::firstOrCreate(
                [
                    'station_id' => $stationId,
                    'name'       => $transporter->name,
                ],
                [
                    'phone'      => $transporter->phone   ?? null,
                    'email'      => $transporter->email   ?? null,
                    'address'    => $transporter->address ?? null,
                    'is_active'  => true,
                    'notes'      => 'Client généré depuis dépotage (contre-plein).',
                ]
            );

            CreditTopup::create([
                'station_id' => $stationId,
                'client_id'  => $client->id,
                'amount'     => $totalContreValeur,
                'date'       => $data['date_reception'],
                'notes'      => 'Contre-plein (réception #'.$reception->id.')',
                'created_by' => auth()->id(),
            ]);
        }

        DB::commit();
        return redirect()
            ->route('fuel-receptions.index')
            ->with('success', 'Réception enregistrée avec succès.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
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



    public function edit($id)
    {
        $stationId = session('selected_station_id');

        $reception = FuelReception::with(['lines', 'station', 'transporter', 'driver'])->findOrFail($id);
        $tanks = Tank::where('station_id', $stationId)->get();
        $transporters = Transporter::where('station_id', $stationId)->get();
        $drivers = Driver::where('station_id', $stationId)->get();

        return view('fuel_receptions.edit', compact('reception', 'tanks', 'transporters', 'drivers'));
    }

public function update(Request $request, $id)
{
    $reception = FuelReception::with('lines')->findOrFail($id);

    /* 1. VALIDATION ------------------------------------------------- */
    $data = $request->validate([
        'date_reception'     => 'required|date',
        'rotation'           => 'required|in:6-14,14-22,22-6',
        'num_bl'             => 'nullable|string',
        'transporter_id'     => 'nullable|exists:transporters,id',
        'driver_id'          => 'nullable|exists:drivers,id',
        'vehicle_registration' => ['nullable','string','max:30'],
        'remarques'            => 'nullable|string',

        'tanks.*.tank_id'            => 'required|exists:tanks,id',
        'tanks.*.jauge_avant'        => 'nullable|numeric',
        'tanks.*.reception_par_cuve' => 'nullable|numeric',
        'tanks.*.jauge_apres'        => 'nullable|numeric',
        'tanks.*.contre_plein_litre' => 'nullable|numeric|min:0',
    ]);

    /* 2. CUVE UNIQUE ------------------------------------------------ */
    $tankIds = array_column($data['tanks'], 'tank_id');
    if (count($tankIds) !== count(array_unique($tankIds))) {
        return back()->withErrors(['error'=>'Une même cuve ne peut pas apparaître plusieurs fois.']);
    }

    DB::beginTransaction();
    try {
        /* 3. BUFFER LIGNES ---------------------------------------- */
        $tempLines   = [];
        $newContre   = 0;            // total contre-plein valeur nouvelle
        foreach ($data['tanks'] as $line) {
            $tank = Tank::with('product')->findOrFail($line['tank_id']);

            $jAvant   = $line['jauge_avant']        ?? 0;
            $jApres   = $line['jauge_apres']        ?? 0;
            $qteRec   = $line['reception_par_cuve'] ?? 0;
            $cpl      = $line['contre_plein_litre'] ?? 0;
            $uPrice   = $tank->product->price;
            $cplVal   = $cpl * $uPrice;

            if ($jApres > $tank->capacite) {
                throw new \Exception("La jauge après dépasse la capacité de {$tank->code}");
            }

            $tempLines[] = [
                'tank'             => $tank,
                'jauge_avant'      => $jAvant,
                'jauge_apres'      => $jApres,
                'quantite'         => $qteRec,
                'contre_litres'    => $cpl,
                'contre_valeur'    => $cplVal,
                'ecart_reception'  => ($jApres - $jAvant) - ($qteRec - $cpl),
                'ecart_stock'      => $jAvant - ($tank->stock->quantite_actuelle ?? 0),
            ];
            $newContre += $cplVal;
        }

        /* 4. MÀJ ENTÊTE RÉCEPTION -------------------------------- */
        $reception->update([
            'date_reception'       => $data['date_reception'],
            'rotation'             => $data['rotation'],
            'num_bl'               => $data['num_bl']           ?? null,
            'transporter_id'       => $data['transporter_id']   ?? null,
            'driver_id'            => $data['driver_id']        ?? null,
            'vehicle_registration' => $data['vehicle_registration'] ?? null,
            'remarques'            => $data['remarques']        ?? null,
        ]);

        /* 5. RESTAURE STOCK & SUPPR ANCIENNES LIGNES -------------- */
        $oldContre = 0;                                   // total contre-plein valeur ancien
        foreach ($reception->lines as $old) {
            $stock = TankStock::firstOrNew(['tank_id'=>$old->tank_id]);
            $netOld = ($old->reception_par_cuve ?? 0) - ($old->contre_plein_litre ?? 0);
            $stock->quantite_actuelle = max($stock->quantite_actuelle - $netOld,0);
            $stock->save();

            $oldContre += ($old->contre_plein_valeur ?? 0);
            $old->delete();
        }

        /* 6. INSÈRE NOUVELLES LIGNES + STOCK ---------------------- */
        foreach ($tempLines as $l) {
            $reception->lines()->create([
                'tank_id'             => $l['tank']->id,
                'jauge_avant'         => $l['jauge_avant'],
                'reception_par_cuve'  => $l['quantite'],
                'contre_plein_litre'  => $l['contre_litres'],
                'contre_plein_valeur' => $l['contre_valeur'],
                'jauge_apres'         => $l['jauge_apres'],
                'ecart_reception'     => $l['ecart_reception'],
                'ecart_stock'         => $l['ecart_stock'],
            ]);

            TankStock::updateOrCreate(
                ['tank_id'=>$l['tank']->id],
                ['quantite_actuelle'=>$l['jauge_apres']]
            );
        }

        /* 7. MÀJ / CRÉE CREDIT TOPUP ------------------------------ */
        $note = 'Contre-plein (réception #'.$reception->id.')';
        $credit = CreditTopup::where('station_id',$reception->station_id)
                  ->where('notes',$note)->first();

        if ($newContre > 0) {
            // client transporteur
            $transporter = Transporter::find($reception->transporter_id);
            $client = Client::firstOrCreate(
                ['station_id'=>$reception->station_id,'name'=>$transporter->name],
                ['phone'=>$transporter->phone ?? null,
                 'email'=>$transporter->email ?? null,
                 'address'=>$transporter->address ?? null,
                 'is_active'=>true,
                 'notes'=>'Client généré depuis dépotage (contre-plein)']
            );

            if ($credit) {
                $credit->update(['client_id'=>$client->id,'amount'=>$newContre,'date'=>$reception->date_reception]);
            } else {
                CreditTopup::create([
                    'station_id'=>$reception->station_id,
                    'client_id' =>$client->id,
                    'amount'    =>$newContre,
                    'date'      =>$reception->date_reception,
                    'notes'     =>$note,
                    'created_by'=>auth()->id(),
                ]);
            }
        } elseif ($credit) {
            // plus de contre-plein : on supprime la créance
            $credit->delete();
        }

        DB::commit();
        return redirect()->route('fuel-receptions.index')
                         ->with('success','Dépotage mis à jour avec succès.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error'=>$e->getMessage()]);
    }
}



    public function show($id)
    {
        $reception = FuelReception::with(['station', 'transporter', 'driver', 'lines.tank'])->findOrFail($id);

        return view('fuel_receptions.show', compact('reception'));
    }

    public function export($id)
    {
        $reception = FuelReception::findOrFail($id);

        return Excel::download(new FuelReceptionExport($reception), 'depotage_'.$reception->date_reception.'.xlsx');
    }

    public function exportPdf($id)
    {
        $reception = FuelReception::with(['station', 'transporter', 'driver', 'lines.tank'])->findOrFail($id);

        $pdf = Pdf::loadView('exports.reception_pdf', compact('reception'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('depotage_'.$reception->date_reception.'.pdf');
    }

    public function destroy($id)
    {
        $reception = FuelReception::with('lines')->findOrFail($id);

        DB::beginTransaction();

        try {
            // 1. Réajuster les stocks de chaque cuve
            foreach ($reception->lines as $line) {
                $stock = TankStock::firstOrNew(['tank_id' => $line->tank_id]);
                $stock->quantite_actuelle -= $line->reception_par_cuve ?? 0;
                $stock->quantite_actuelle = max($stock->quantite_actuelle, 0);
                $stock->save();
            }

            // 2. Supprimer les lignes de réception
            $reception->lines()->delete();

            // 3. Supprimer la fiche réception
            $reception->delete();

            DB::commit();

            return redirect()->route('fuel-receptions.index')->with('success', 'Dépotage supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Échec de suppression : '.$e->getMessage()]);
        }
    }
}
