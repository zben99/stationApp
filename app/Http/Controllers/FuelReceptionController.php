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

        $receptions = FuelReception::with(['station', 'transporter', 'driver'])
            ->where('station_id', $stationId)
            ->orderBy('date_reception', 'desc')
            ->paginate(10);

        return view('fuel_receptions.index', compact('receptions'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
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
            'num_bl' => 'nullable|string',
            'transporter_id' => 'nullable|exists:transporters,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'remarques' => 'nullable|string',
            'tanks.*.tank_id' => 'required|exists:tanks,id',
            'tanks.*.jauge_avant' => 'nullable|numeric',
            'tanks.*.reception_par_cuve' => 'nullable|numeric',
            'tanks.*.jauge_apres' => 'nullable|numeric',
        ]);

        $data['station_id'] = session('selected_station_id');

        DB::beginTransaction();

        try {
            $reception = FuelReception::create([
                'station_id' => $data['station_id'],
                'date_reception' => $data['date_reception'],
                'num_bl' => $data['num_bl'] ?? null,
                'transporter_id' => $data['transporter_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'remarques' => $data['remarques'] ?? null,
            ]);

            foreach ($data['tanks'] as $line) {
                $tank = Tank::findOrFail($line['tank_id']);
                $stock = TankStock::firstOrNew(['tank_id' => $tank->id]);

                $quantiteActuelle = $stock->quantite_actuelle ?? 0;
                $quantiteReception = $line['reception_par_cuve'] ?? 0;
                $quantiteProjetee = $quantiteActuelle + $quantiteReception;

                if ($quantiteProjetee > $tank->capacite) {
                    throw new \Exception("La réception dépasse la capacité de la cuve '{$tank->name}'. Capacité : {$tank->capacity}, tentative : {$quantiteProjetee}");
                }

                FuelReceptionLine::create([
                    'fuel_reception_id' => $reception->id,
                    'tank_id' => $tank->id,
                    'jauge_avant' => $line['jauge_avant'] ?? null,
                    'reception_par_cuve' => $quantiteReception,
                    'jauge_apres' => $line['jauge_apres'] ?? null,
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
            'num_bl' => 'nullable|string',
            'transporter_id' => 'nullable|exists:transporters,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'remarques' => 'nullable|string',
            'tanks.*.tank_id' => 'required|exists:tanks,id',
            'tanks.*.jauge_avant' => 'nullable|numeric',
            'tanks.*.reception_par_cuve' => 'nullable|numeric',
            'tanks.*.jauge_apres' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            // 🧼 1. Réajuster les stocks en supprimant les anciens apports
            foreach ($reception->lines as $oldLine) {
                $stock = TankStock::firstOrNew(['tank_id' => $oldLine->tank_id]);
                $stock->quantite_actuelle -= $oldLine->reception_par_cuve ?? 0;
                $stock->quantite_actuelle = max($stock->quantite_actuelle, 0);
                $stock->save();
                $oldLine->delete();
            }

            // 📝 2. Mettre à jour la fiche
            $reception->update([
                'date_reception' => $data['date_reception'],
                'num_bl' => $data['num_bl'] ?? null,
                'transporter_id' => $data['transporter_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'remarques' => $data['remarques'] ?? null,
            ]);

            // ✅ 3. Réinsérer les nouvelles lignes et maj stock
            foreach ($data['tanks'] as $line) {
                $tank = Tank::findOrFail($line['tank_id']);
                $receptionLine = $reception->lines()->create([
                    'tank_id' => $tank->id,
                    'jauge_avant' => $line['jauge_avant'] ?? null,
                    'reception_par_cuve' => $line['reception_par_cuve'] ?? null,
                    'jauge_apres' => $line['jauge_apres'] ?? null,
                    'produit' => $tank->product->name ?? '-',
                ]);

                TankStock::updateOrCreate(
                    ['tank_id' => $tank->id],
                    ['quantite_actuelle' => DB::raw('quantite_actuelle + ' . ($line['reception_par_cuve'] ?? 0))]
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

    public function destroy(FuelReception $fuelReception)
    {
        $stock = TankStock::where('tank_id', $fuelReception->tank_id)->first();
        if ($stock) {
            $stock->quantite_actuelle -= $fuelReception->quantite_livree;
            $stock->save();
        }

        $fuelReception->delete();
        return redirect()->route('fuel-receptions.index')->with('success', 'Réception supprimée.');
    }
}
