<?php

namespace App\Http\Controllers;

use App\Models\DailyProductSale;
use App\Models\ProductPackaging;
use Illuminate\Http\Request;

class DailyProductSaleController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');

        $sales = DailyProductSale::where('station_id', $stationId)
            ->orderByDesc('date')
            ->orderBy('rotation')
            ->get()
            ->groupBy(fn ($s) => $s->date->format('Y-m-d').'|'.$s->rotation);

        return view('daily_product_sales.index', compact('sales'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');

        // Récupère les produits de type lubrifiant, PEA, GAZ ou lampe
        $products = ProductPackaging::with('product', 'packaging')
            ->whereHas('product.stationCategory', function ($q) use ($stationId) {
                $q->where('station_id', $stationId)
                    ->whereIn('type', ['lubrifiant', 'pea', 'gaz', 'lampe']);
            })->get();

        return view('daily_product_sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
            'sales' => 'required|array|min:1',
            'sales.*.product_packaging_id' => 'required|exists:station_product_packaging,id',
            'sales.*.quantity' => 'required|numeric|min:0.01',
            'sales.*.unit_price' => 'required|numeric|min:0',
        ]);

        $stationId = session('selected_station_id');
        $date = $request->date;
        $rotation = $request->rotation;

        // ✅ Vérifier si des ventes existent déjà pour cette date + rotation
        $alreadyExists = \App\Models\DailyProductSale::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors(['rotation' => 'Des données existent déjà pour cette date et cette rotation.'])->withInput();
        }

        // ✅ Enregistrement des lignes
        foreach ($request->sales as $line) {
            $quantity = $line['quantity'];
            $unitPrice = $line['unit_price'];
            $totalPrice = $quantity * $unitPrice;

            DailyProductSale::create([
                'station_id' => $stationId,
                'product_packaging_id' => $line['product_packaging_id'],
                'date' => $date,
                'rotation' => $rotation,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);
        }

        return redirect()->route('daily-product-sales.index')->with('success', 'Recette enregistrée avec succès.');
    }

    public function show($date, $rotation)
    {
        $stationId = session('selected_station_id');

        $entries = DailyProductSale::with([
            'productPackaging.packaging',
            'productPackaging.product',
        ])
            ->where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->get();

        if ($entries->isEmpty()) {
            return redirect()->route('daily-product-sales.index')
                ->with('error', 'Aucune donnée trouvée pour cette date et rotation.');
        }

        return view('daily_product_sales.show', [
            'entries' => $entries,
            'date' => $date,
            'rotation' => $rotation,
        ]);
    }
}
