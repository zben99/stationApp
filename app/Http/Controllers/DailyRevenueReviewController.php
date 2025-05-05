<?php

namespace App\Http\Controllers;

use App\Models\DailyProductSale;
use App\Models\DailyRevenueReview;
use App\Models\DailySimpleRevenue;
use App\Models\FuelIndex;
use Illuminate\Http\Request;

class DailyRevenueReviewController extends Controller
{
    public function index()
    {

        $stationId = session('selected_station_id');

        $reviews = DailyRevenueReview::where('station_id', $stationId)
            ->orderByDesc('date')
            ->orderBy('rotation')
            ->get();

        return view('daily_revenue_reviews.index', compact('reviews'));
    }

    public function create()
    {
        $stationId = session('selected_station_id');
        $date = request('date', now()->format('Y-m-d'));
        $rotation = request('rotation', '6-14');

        // 🔸 Total Fuel à partir des indexes
        $fuelTotal = FuelIndex::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->get()
            ->sum(fn ($item) => $item->montant_total);

        // 🔸 Total Produits (Lubrifiants, PEA, Gaz, Lampes)
        $productTotal = DailyProductSale::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->sum('total_price');

        // 🔸 Total Boutique et Lavage
        $shopTotal = DailySimpleRevenue::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->sum('amount');

        return view('daily_revenue_reviews.create', compact(
            'fuelTotal', 'productTotal', 'shopTotal'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',
        ]);

        $stationId = session('selected_station_id');
        $date = $request->input('date');
        $rotation = $request->input('rotation');

        // ⚠️ Vérifie si déjà validé
        $exists = DailyRevenueReview::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->exists();

        if ($exists) {
            return back()->withErrors('Cette rotation est déjà validée pour cette date.');
        }

        // 🧮 Calcul des montants
        $fuelTotal = FuelIndex::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->get()
            ->sum(fn ($item) => $item->montant_total);

        $productTotal = DailyProductSale::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->sum('total_price');

        $shopTotal = DailySimpleRevenue::where('station_id', $stationId)
            ->whereDate('date', $date)
            ->where('rotation', $rotation)
            ->sum('amount');

        $review = DailyRevenueReview::create([
            'station_id' => $stationId,
            'date' => $date,
            'rotation' => $rotation,
            'fuel_amount' => $fuelTotal,
            'product_amount' => $productTotal,
            'shop_amount' => $shopTotal,
            'total_amount' => $fuelTotal + $productTotal + $shopTotal,
        ]);

        // ✅ Corrigé : on passe bien l’ID
        return redirect()->route('daily-revenue-review.show', ['daily_revenue_review' => $review->id])
            ->with('success', 'La revue de recette a été validée avec succès.');
    }

    public function show(DailyRevenueReview $daily_revenue_review)
    {
        return view('daily_revenue_reviews.show', [
            'review' => $daily_revenue_review,
        ]);
    }
}
