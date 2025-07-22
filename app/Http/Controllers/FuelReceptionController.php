<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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
use Illuminate\Support\Facades\DB;
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

        $tanks = Tank::where('station_id', $stationId)
            ->orderBy('code')
            ->get();
        $transporters = Transporter::where('station_id', $stationId)
            ->orderBy('name')
            ->get();
        $drivers = Driver::where('station_id', $stationId)
            ->orderBy('name')
            ->get();

        return view('fuel_receptions.create', compact('tanks', 'transporters', 'drivers'));
    }

    public function store(Request $request)
    {
        /* 1° VALIDATION ---------------------------------------------------- */
        $data = $request->validate([
            'date_reception' => ['required', 'date'],
            'rotation' => ['required', 'in:6-14,14-22,22-6'],
            'num_bl' => ['nullable', 'string', 'max:255'],
            'transporter_id' => ['required', 'string', 'max:255'],
            'driver_id' => ['required', 'string', 'max:255'],
            'vehicle_registration' => ['nullable', 'string', 'max:30'],
            'remarques' => ['nullable', 'string'],

            'tanks.*.tank_id' => ['required', 'exists:tanks,id'],
            'tanks.*.jauge_avant' => ['nullable', 'numeric'],
            'tanks.*.reception_par_cuve' => ['nullable', 'numeric'],
            'tanks.*.jauge_apres' => ['nullable', 'numeric'],
            'tanks.*.contre_plein_litre' => ['nullable', 'numeric', 'min:0'],
        ]);

        /* 2° CONTRÔLE D’UNICITÉ DES CUVES --------------------------------- */
        $tankIds = array_column($data['tanks'], 'tank_id');
        if (count($tankIds) !== count(array_unique($tankIds))) {
            return back()->withErrors(['error' => 'Une même cuve ne peut pas apparaître plusieurs fois.'])
                ->withInput();
        }

        /* 3° STATION + RÉSOLUTION TRANSPORTEUR / CHAUFFEUR ---------------- */
        $stationId = session('selected_station_id');
        if (! $stationId) {
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
                'station_id' => $stationId,
                'date_reception' => $data['date_reception'],
                'rotation' => $data['rotation'],
                'num_bl' => $data['num_bl'] ?? null,
                'transporter_id' => $data['transporter_id'],
                'driver_id' => $data['driver_id'],
                'vehicle_registration' => $data['vehicle_registration'] ?? null,
                'remarques' => $data['remarques'] ?? null,
            ]);

            $totalContreValeur = 0;

            /* 4.2 Parcours des cuves */
            foreach ($data['tanks'] as $line) {
                $tank = Tank::findOrFail($line['tank_id']);
                $unitPrice = $tank->product->price;
                $stockActuel = $tank->stock->quantite_actuelle ?? 0;

                $jaugeAvant = $line['jauge_avant'] ?? 0;
                $jaugeApres = $line['jauge_apres'] ?? 0;
                $qteReception = $line['reception_par_cuve'] ?? 0;
                $contreLitres = $line['contre_plein_litre'] ?? 0;
                $contreValeur = $contreLitres * $unitPrice;

                /* sécurité capacité */
                $qteProjetee = $stockActuel + $qteReception - $contreLitres;
                if ($qteProjetee > $tank->capacite) {
                    throw new \Exception("Dépassement de capacité sur la cuve {$tank->code}.");
                }

                FuelReceptionLine::create([
                    'fuel_reception_id' => $reception->id,
                    'tank_id' => $tank->id,
                    'jauge_avant' => $jaugeAvant ?: null,
                    'reception_par_cuve' => $qteReception,
                    'contre_plein_litre' => $contreLitres,
                    'contre_plein_valeur' => $contreValeur,
                    'jauge_apres' => $jaugeApres ?: null,
                    'ecart_reception' => ($jaugeApres - $jaugeAvant) - ($qteReception),
                    'ecart_stock' => $jaugeAvant - $stockActuel,
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
                        'name' => $transporter->name,
                    ],
                    [
                        'phone' => $transporter->phone ?? null,
                        'email' => $transporter->email ?? null,
                        'address' => $transporter->address ?? null,
                        'is_active' => true,
                        'notes' => 'Client généré depuis dépotage (contre-plein).',
                    ]
                );

                CreditTopup::create([
                    'station_id' => $stationId,
                    'client_id' => $client->id,
                    'amount' => $totalContreValeur,
                    'date' => $data['date_reception'],
                    'notes' => 'Contre-plein (réception #'.$reception->id.')',
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
        $tanks = Tank::where('station_id', $stationId)
            ->orderBy('code')
            ->get();
        $transporters = Transporter::where('station_id', $stationId)
            ->orderBy('name')
            ->get();
        $drivers = Driver::where('station_id', $stationId)
            ->orderBy('name')
            ->get();

        return view('fuel_receptions.edit', compact('reception', 'tanks', 'transporters', 'drivers'));
    }

    public function update(Request $request, FuelReception $reception)
    {
        $request->validate([
            'cuve_id.*' => 'required',
            'jauge_avant.*' => 'required|numeric',
            'jauge_apres.*' => 'required|numeric',
            'reception_par_cuve.*' => 'required|numeric',
            'contre_plein.*' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            // Supprimer les anciennes lignes et remettre à jour le stock
            foreach ($reception->lines as $old) {
                $stock = TankStock::firstOrNew(['tank_id' => $old->tank_id]);
                $netOld = ($old->reception_par_cuve ?? 0) - ($old->contre_plein_litre ?? 0);
                $stock->quantite_actuelle = max($stock->quantite_actuelle - $netOld, 0);
                $stock->save();
            }

            $reception->lines()->delete();

            $reception->update([
                'date_reception' => $request->date_reception,
                'rotation' => $request->rotation,
                'updated_by' => Auth::id(),
            ]);

            foreach ($request->cuve_id as $index => $cuve_id) {
                $jAvant = $request->jauge_avant[$index];
                $jApres = $request->jauge_apres[$index];
                $qteRec = $request->reception_par_cuve[$index];
                $cpl = $request->contre_plein[$index] ?? 0;

                $variation = $jApres - $jAvant;
                $livreNet = $qteRec + $cpl;
                $ecart = $livreNet - $variation;

                $reception->lines()->create([
                    'tank_id' => $cuve_id,
                    'jauge_avant' => $jAvant,
                    'jauge_apres' => $jApres,
                    'reception_par_cuve' => $qteRec,
                    'contre_plein_litre' => $cpl,
                    'ecart_reception' => $ecart,
                ]);

                // Mise à jour du stock
                $stock = TankStock::firstOrNew(['tank_id' => $cuve_id]);
                $net = $qteRec - $cpl;
                $stock->quantite_actuelle = $stock->quantite_actuelle + $net;
                $stock->save();
            }

            DB::commit();

            return redirect()->route('fuel-receptions.index')->with('success', 'Réception modifiée avec succès');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', 'Erreur: '.$th->getMessage());
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
