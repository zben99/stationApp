<?php

namespace App\Http\Controllers;

use App\Exports\FuelReceptionExport;
use App\Models\Driver;
use App\Models\FuelReception;
use App\Models\FuelReceptionLine;
use App\Models\Tank;
use App\Models\TankStock;
use App\Models\Transporter;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
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
        /* 1. Validation ----------------------------------------------------- */
        $data = $request->validate([
            'date_reception'                 => ['required', 'date'],
            'rotation'                       => ['required', 'in:6-14,14-22,22-6'],
            'num_bl'                         => ['nullable', 'string', 'max:255'],
            'transporter_id'                 => ['required', 'string', 'max:255'],
            'driver_id'                      => ['required', 'string', 'max:255'],
            'vehicle_registration'           => ['nullable', 'string', 'max:30'],
            'remarques'                      => ['nullable', 'string'],

            'tanks.*.tank_id'                => ['required', 'exists:tanks,id'],
            'tanks.*.jauge_avant'            => ['nullable', 'numeric'],
            'tanks.*.reception_par_cuve'     => ['nullable', 'numeric'],
            'tanks.*.jauge_apres'            => ['nullable', 'numeric'],
            'tanks.*.contre_plein_litre'     => ['nullable', 'numeric', 'min:0'],
        ]);

        /* 2. Unicité cuves -------------------------------------------------- */
        $tankIds = array_column($data['tanks'], 'tank_id');
        if (count($tankIds) !== count(array_unique($tankIds))) {
            return back()->withErrors(['error' => 'Une même cuve ne peut pas apparaître plusieurs fois.'])
                        ->withInput();
        }

        /* 3. Résolution transporteur / chauffeur --------------------------- */
        $stationId = session('selected_station_id');
        if (!$stationId) {
            return back()->withErrors(['error' => 'Aucune station sélectionnée.'])->withInput();
        }
        $data['station_id'] = $stationId;

        $data['transporter_id'] = $this->resolveEntityId(
            model: Transporter::class,
            value: $data['transporter_id'],
            stationId: $stationId
        );
        $data['driver_id'] = $this->resolveEntityId(
            model: Driver::class,
            value: $data['driver_id'],
            stationId: $stationId
        );

        /* 4. Transaction ---------------------------------------------------- */
        DB::beginTransaction();

        try {
            $reception = FuelReception::create([
                'station_id'           => $data['station_id'],
                'date_reception'       => $data['date_reception'],
                'rotation'             => $data['rotation'],
                'num_bl'               => $data['num_bl']           ?? null,
                'transporter_id'       => $data['transporter_id'],
                'driver_id'            => $data['driver_id'],
                'vehicle_registration' => $data['vehicle_registration'] ?? null,
                'remarques'            => $data['remarques']        ?? null,
            ]);

            foreach ($data['tanks'] as $line) {
                $tank        = Tank::findOrFail($line['tank_id']);
                $unitPrice   = $tank->product->price;                 // prix du produit
                $stock       = TankStock::firstOrNew(['tank_id' => $tank->id]);

                $stock_actuel  = $stock->quantite_actuelle ?? 0;
                $jauge_avant   = $line['jauge_avant']        ?? 0;
                $jauge_apres   = $line['jauge_apres']        ?? 0;
                $quantite      = $line['reception_par_cuve'] ?? 0;
                $contrePlein   = $line['contre_plein_litre'] ?? 0;
                $contreValeur  = $contrePlein * $unitPrice;          // <-- calcul valeur

                $quantiteProjetee = $stock_actuel + $quantite - $contrePlein;
                $ecart_reception  = ($jauge_apres - $jauge_avant) - ($quantite - $contrePlein);
                $ecart_stock      = $jauge_avant - $stock_actuel;

                if ($quantiteProjetee > $tank->capacite) {
                    throw new \Exception(
                        "La réception dépasse la capacité de la cuve '{$tank->code}'. " .
                        "Capacité : {$tank->capacite}, tentative : {$quantiteProjetee}"
                    );
                }

                FuelReceptionLine::create([
                    'fuel_reception_id'   => $reception->id,
                    'tank_id'             => $tank->id,
                    'jauge_avant'         => $jauge_avant ?: null,
                    'reception_par_cuve'  => $quantite,
                    'contre_plein_litre'  => $contrePlein,
                    'contre_plein_valeur' => $contreValeur,           // <-- sauvegarde
                    'jauge_apres'         => $jauge_apres ?: null,
                    'ecart_reception'     => $ecart_reception,
                    'ecart_stock'         => $ecart_stock,
                ]);

                TankStock::updateOrCreate(
                    ['tank_id' => $tank->id],
                    ['quantite_actuelle' => $quantiteProjetee]
                );


            }

            DB::commit();
            return redirect()->route('fuel-receptions.index')
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

        /* -------- 1. VALIDATION ----------------------------------------- */
        $data = $request->validate([
            'date_reception'                 => 'required|date',
            'rotation'                       => 'required|in:6-14,14-22,22-6',
            'num_bl'                         => 'nullable|string',
            'transporter_id'                 => 'nullable|exists:transporters,id',
            'driver_id'                      => 'nullable|exists:drivers,id',
            'vehicle_registration'           => ['nullable', 'string', 'max:30'],
            'remarques'                      => 'nullable|string',

            'tanks.*.tank_id'                => 'required|exists:tanks,id',
            'tanks.*.jauge_avant'            => 'nullable|numeric',
            'tanks.*.reception_par_cuve'     => 'nullable|numeric',
            'tanks.*.jauge_apres'            => 'nullable|numeric',
            'tanks.*.contre_plein_litre'     => 'nullable|numeric|min:0',
        ]);

        /* -------- 2. DÉTECTION DOUBLON CUVE ----------------------------- */
        $tankIds = array_column($data['tanks'], 'tank_id');
        if (count($tankIds) !== count(array_unique($tankIds))) {
            return back()->withErrors(['error' => 'Une même cuve ne peut pas apparaître plusieurs fois.']);
        }

        DB::beginTransaction();

        try {
            /* -------- 3. PRÉ-VALIDATION & BUFFER ------------------------ */
            $tempLines = [];

            foreach ($data['tanks'] as $line) {
                $tank  = Tank::with('product')->findOrFail($line['tank_id']);
                $stock = TankStock::firstOrNew(['tank_id' => $tank->id]);

                $stock_actuel  = $stock->quantite_actuelle ?? 0;
                $jauge_avant   = $line['jauge_avant']        ?? 0;
                $jauge_apres   = $line['jauge_apres']        ?? 0;
                $quantite      = $line['reception_par_cuve'] ?? 0;
                $contrePlein   = $line['contre_plein_litre'] ?? 0;
                $unitPrice     = $tank->product->price;
                $contreValeur  = $contrePlein * $unitPrice;

                if ($jauge_apres > $tank->capacite) {
                    throw new \Exception(
                        "La jauge après dépasse la capacité de la cuve '{$tank->code}' " .
                        "(max: {$tank->capacite} L, reçu: {$jauge_apres} L)."
                    );
                }

                $tempLines[] = [
                    'tank'             => $tank,
                    'stock'            => $stock,
                    'jauge_avant'      => $jauge_avant,
                    'jauge_apres'      => $jauge_apres,
                    'quantite'         => $quantite,
                    'contre_plein'     => $contrePlein,
                    'contre_valeur'    => $contreValeur,
                    'stock_actuel'     => $stock_actuel,
                    'ecart_reception'  => ($jauge_apres - $jauge_avant) - ($quantite - $contrePlein),
                    'ecart_stock'      => $jauge_avant - $stock_actuel,
                ];
            }

            /* -------- 4. MAJ ENTÊTE RÉCEPTION --------------------------- */
            $reception->update([
                'date_reception'       => $data['date_reception'],
                'rotation'             => $data['rotation'],
                'num_bl'               => $data['num_bl']           ?? null,
                'transporter_id'       => $data['transporter_id']   ?? null,
                'driver_id'            => $data['driver_id']        ?? null,
                'vehicle_registration' => $data['vehicle_registration'] ?? null,
                'remarques'            => $data['remarques']        ?? null,
            ]);

            /* -------- 5. ANNULATION ANCIENNES LIGNES + STOCK ------------ */
            foreach ($reception->lines as $oldLine) {
                $stock = TankStock::firstOrNew(['tank_id' => $oldLine->tank_id]);
                // on retire le NET réellement ajouté lors du précédent enregistrement
                $netOld = ($oldLine->reception_par_cuve ?? 0) - ($oldLine->contre_plein_litre ?? 0);
                $stock->quantite_actuelle = max($stock->quantite_actuelle - $netOld, 0);
                $stock->save();
                $oldLine->delete();
            }

            /* -------- 6. INSERT NOUVELLES LIGNES + STOCK + MOUV. -------- */
            foreach ($tempLines as $line) {
                $reception->lines()->create([
                    'tank_id'              => $line['tank']->id,
                    'jauge_avant'          => $line['jauge_avant'],
                    'reception_par_cuve'   => $line['quantite'],
                    'contre_plein_litre'   => $line['contre_plein'],
                    'contre_plein_valeur'  => $line['contre_valeur'],
                    'jauge_apres'          => $line['jauge_apres'],
                    'ecart_reception'      => $line['ecart_reception'],
                    'ecart_stock'          => $line['ecart_stock'],
                ]);

                TankStock::updateOrCreate(
                    ['tank_id' => $line['tank']->id],
                    ['quantite_actuelle' => $line['jauge_apres']]
                );


            }

            DB::commit();
            return redirect()->route('fuel-receptions.index')
                            ->with('success', 'Dépotage mis à jour avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
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
