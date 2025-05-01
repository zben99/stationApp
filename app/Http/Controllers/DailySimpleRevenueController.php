<?php

namespace App\Http\Controllers;

use App\Models\DailySimpleRevenue;
use App\Models\Station;
use Illuminate\Http\Request;

class DailySimpleRevenueController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');
        $revenues = DailySimpleRevenue::where('station_id', $stationId)
            ->orderByDesc('date')
            ->orderByDesc('rotation')
            ->paginate(15);

        return view('daily_simple_revenues.index', compact('revenues'));
    }

    public function create()
    {
        return view('daily_simple_revenues.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'boutique' => 'required|numeric|min:0',
            'lavage' => 'required|numeric|min:0',
        ]);

        $stationId = session('selected_station_id');
        $userId = auth()->id();

        foreach (['boutique', 'lavage'] as $type) {
            $exists = DailySimpleRevenue::where([
                'station_id' => $stationId,
                'date' => $data['date'],
                'rotation' => $data['rotation'],
                'type' => $type
            ])->exists();

            if ($exists) {
                return back()->withErrors(['type' => "Une recette pour \"$type\" existe déjà pour cette date et rotation."])->withInput();
            }

            DailySimpleRevenue::create([
                'station_id' => $stationId,
                'date' => $data['date'],
                'rotation' => $data['rotation'],
                'type' => $type,
                'amount' => $data[$type],
                'created_by' => $userId,
            ]);
        }

        return redirect()->route('daily-simple-revenues.index')->with('success', 'Recettes enregistrées avec succès.');
    }


    public function edit(DailySimpleRevenue $dailySimpleRevenue)
    {
        return view('daily_simple_revenues.edit', compact('dailySimpleRevenue'));
    }

    public function update(Request $request, DailySimpleRevenue $dailySimpleRevenue)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'type' => 'required|in:boutique,lavage',
            'amount' => 'required|numeric|min:0',
        ]);

        // Si on change les champs clés, s'assurer de l'unicité
        $exists = DailySimpleRevenue::where([
                'station_id' => $dailySimpleRevenue->station_id,
                'date' => $data['date'],
                'rotation' => $data['rotation'],
                'type' => $data['type'],
            ])
            ->where('id', '!=', $dailySimpleRevenue->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['type' => 'Une recette existe déjà pour cette date, rotation et type.'])->withInput();
        }

        $dailySimpleRevenue->update($data);
        return redirect()->route('daily-simple-revenues.index')->with('success', 'Recette mise à jour.');
    }

    public function destroy(DailySimpleRevenue $dailySimpleRevenue)
    {
        $dailySimpleRevenue->delete();
        return back()->with('success', 'Recette supprimée.');
    }
}
