<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Tank;
use App\Models\Driver;
use App\Models\Station;
use App\Models\Supplier;
use App\Models\TankStock;
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
        $data = $request->validate([
            'date_reception' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'num_bl' => 'nullable|string',
            'transporter_id' => 'nullable|exists:transporters,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'remarques' => 'nullable|string',
            'tanks.*.tank_id' => 'required|exists:tanks,id',
            'tanks.*.jauge_avant' => 'nullable|numeric',
            'tanks.*.reception_par_cuve' => 'nullable|numeric',
            'tanks.*.jauge_apres' => 'nullable|numeric',
        ]);

        $tankIds = array_column($data['tanks'], 'tank_id');
        if (count($tankIds) !== count(array_unique($tankIds))) {
            return redirect()->back()->withErrors(['error' => 'Une même cuve ne peut pas apparaître plusieurs fois.']);
        }
        $data['station_id'] = session('selected_station_id');

        DB::beginTransaction();

        try {
            $reception = FuelReception::create([
                'station_id' => $data['station_id'],
                'date_reception' => $data['date_reception'],
                'rotation' => $data['rotation'],
                'num_bl' => $data['num_bl'] ?? null,
                'transporter_id' => $data['transporter_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'remarques' => $data['remarques'] ?? null,
            ]);

            foreach ($data['tanks'] as $line) {
                $tank = Tank::findOrFail($line['tank_id']);
                $stock = TankStock::firstOrNew(['tank_id' => $tank->id]);





                $stock_actuel = $stock->quantite_actuelle ?? 0;
                $jauge_avant = $line['jauge_avant'] ?? 0;
                $jauge_apres = $line['jauge_apres'] ?? 0;
                $quantite = $line['reception_par_cuve'] ?? 0;

                $quantiteProjetee = $stock_actuel + $quantite;
                $ecart_reception = ($jauge_apres - $jauge_avant) - $quantite;
                $ecart_stock = $jauge_avant - $stock_actuel;


                if ($quantiteProjetee > $tank->capacite) {
                    throw new \Exception("La réception dépasse la capacité de la cuve '{$tank->code}'. Capacité : {$tank->capacity}, tentative : {$quantiteProjetee}");
                }

                FuelReceptionLine::create([
                    'fuel_reception_id' => $reception->id,
                    'tank_id' => $tank->id,
                    'jauge_avant' => $line['jauge_avant'] ?? null,
                    'reception_par_cuve' => $quantite,
                    'jauge_apres' => $line['jauge_apres'] ?? null,
                    'ecart_reception' => $ecart_reception,
                    'ecart_stock' => $ecart_stock,
                ]);

                // Mise à jour du stock
                TankStock::updateOrCreate(
                    ['tank_id' => $tank->id],
                    ['quantite_actuelle' => $quantiteProjetee]
                );
            }

            DB::commit();
            return redirect()->route('fuel-receptions.index')->with('success', 'Réception enregistrée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
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

    $data = $request->validate([
        'date_reception' => 'required|date',
        'rotation' => 'required|in:6-14,14-22,22-6',
        'num_bl' => 'nullable|string',
        'transporter_id' => 'nullable|exists:transporters,id',
        'driver_id' => 'nullable|exists:drivers,id',
        'remarques' => 'nullable|string',
        'tanks.*.tank_id' => 'required|exists:tanks,id',
        'tanks.*.jauge_avant' => 'nullable|numeric',
        'tanks.*.reception_par_cuve' => 'nullable|numeric',
        'tanks.*.jauge_apres' => 'nullable|numeric',
    ]);

    // ❌ Doublon de cuve
    $tankIds = array_column($data['tanks'], 'tank_id');
    if (count($tankIds) !== count(array_unique($tankIds))) {
        return redirect()->back()->withErrors(['error' => 'Une même cuve ne peut pas apparaître plusieurs fois.']);
    }

    DB::beginTransaction();

    try {
        $tempLines = [];

        // 💡 Étape 1 : Prévalider toutes les lignes
        foreach ($data['tanks'] as $line) {
            $tank = Tank::with('product')->findOrFail($line['tank_id']);
            $stock = TankStock::firstOrNew(['tank_id' => $tank->id]);

            $stock_actuel = $stock->quantite_actuelle ?? 0;
            $jauge_avant = $line['jauge_avant'] ?? 0;
            $jauge_apres = $line['jauge_apres'] ?? 0;
            $quantite = $line['reception_par_cuve'] ?? 0;

            if ($jauge_apres > $tank->capacite) {
                throw new \Exception("La jauge après dépasse la capacité de la cuve '{$tank->code}' (max : {$tank->capacite} L, reçu : {$jauge_apres} L).");
            }

            $tempLines[] = [
                'tank' => $tank,
                'stock' => $stock,
                'jauge_avant' => $jauge_avant,
                'jauge_apres' => $jauge_apres,
                'quantite' => $quantite,
                'stock_actuel' => $stock_actuel,
                'ecart_reception' => ($jauge_apres - $jauge_avant) - $quantite,
                'ecart_stock' => $jauge_avant - $stock_actuel,
            ];
        }

        // ✅ Étape 2 : Mise à jour de la fiche réception
        $reception->update([
            'date_reception' => $data['date_reception'],
            'rotation' => $data['rotation'],
            'num_bl' => $data['num_bl'] ?? null,
            'transporter_id' => $data['transporter_id'] ?? null,
            'driver_id' => $data['driver_id'] ?? null,
            'remarques' => $data['remarques'] ?? null,
        ]);

        // 🧽 Étape 3 : Nettoyage sécurisé des anciennes lignes
        foreach ($reception->lines as $oldLine) {
            $stock = TankStock::firstOrNew(['tank_id' => $oldLine->tank_id]);
            $stock->quantite_actuelle -= $oldLine->reception_par_cuve ?? 0;
            $stock->quantite_actuelle = max($stock->quantite_actuelle, 0);
            $stock->save();
            $oldLine->delete();
        }

        // 📌 Étape 4 : Ajout des nouvelles lignes + mise à jour du stock
        foreach ($tempLines as $line) {
            $reception->lines()->create([
                'tank_id' => $line['tank']->id,
                'jauge_avant' => $line['jauge_avant'],
                'reception_par_cuve' => $line['quantite'],
                'jauge_apres' => $line['jauge_apres'],
                'produit' => $line['tank']->product->name ?? '-',
                'ecart_reception' => $line['ecart_reception'],
                'ecart_stock' => $line['ecart_stock'],
            ]);

            TankStock::updateOrCreate(
                ['tank_id' => $line['tank']->id],
                ['quantite_actuelle' => $line['jauge_apres']]
            );
        }

        DB::commit();
        return redirect()->route('fuel-receptions.index')->with('success', 'Dépotage mis à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
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
            return redirect()->back()->withErrors(['error' => "Échec de suppression : " . $e->getMessage()]);
        }
    }

}
