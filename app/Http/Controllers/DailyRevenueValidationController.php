<?php

namespace App\Http\Controllers;

use App\Models\BalanceTopup;
use App\Models\BalanceUsage;
use App\Models\CreditPayment;
use App\Models\CreditTopup;
use App\Models\DailyProductSale;
use App\Models\DailyRevenueValidation;
use App\Models\DailySimpleRevenue;
use App\Models\Expense;
use App\Models\FuelIndex;
use Illuminate\Http\Request;

class DailyRevenueValidationController extends Controller
{
    public function index()
    {
        $stationId = session('selected_station_id');

        $validations = DailyRevenueValidation::where('station_id', $stationId)
            ->orderByDesc('date')
            ->paginate(15);

        return view('daily_revenue_validations.index', compact('validations'));
    }

    public function create()
    {
        /*
        $stationId = session('selected_station_id');
        $date = "2025-05-03";
        $rotation = "6-14";

        if (!$date || !$rotation) {
            return response()->json([
                'message' => 'La date et la rotation sont requises.'
            ], 422);
        }

        $fuel = FuelIndex::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->sum('montant_declare');

        $product = DailyProductSale::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->sum('total_price');

        $shop = DailySimpleRevenue::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->whereIn('type', ['boutique', 'lavage'])
            ->sum('amount');

        $balanceReceived = BalanceTopup::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->sum('amount');

        $balanceUsed = BalanceUsage::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->sum('amount');

        $creditReceived = CreditTopup::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->sum('amount');

        $creditRepaid = CreditPayment::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->sum('amount');

        $expenses = Expense::where('station_id', $stationId)
            ->where('date_depense', $date)
            ->where('rotation', $rotation)
            ->sum('montant');

        return response()->json([
            'fuel_amount' => $fuel,
            'product_amount' => $product,
            'shop_amount' => $shop,
            'balance_received' => $balanceReceived,
            'balance_used' => $balanceUsed,
            'credit_received' => $creditReceived,
            'credit_repaid' => $creditRepaid,
            'expenses' => $expenses,
            'om_amount' => 0,
            'tpe_amount' => 0,
        ]);
*/

        return view('daily_revenue_validations.create');
    }

    public function store(Request $request)
    {
        $stationId = session('selected_station_id');

        $data = $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',

            'fuel_amount' => 'nullable|numeric|min:0',
            'product_amount' => 'nullable|numeric|min:0',
            'shop_amount' => 'nullable|numeric|min:0',
            'om_amount' => 'nullable|numeric|min:0',
            'tpe_amount' => 'nullable|numeric|min:0',

            'balance_received' => 'nullable|numeric|min:0',
            'balance_used' => 'nullable|numeric|min:0',
            'credit_received' => 'nullable|numeric|min:0',
            'credit_repaid' => 'nullable|numeric|min:0',

            'expenses' => 'nullable|numeric|min:0',
            'net_to_deposit' => 'nullable|numeric|min:0',
        ]);

        // Vérifie s'il y a déjà une validation pour cette date et rotation
        $exists = \App\Models\DailyRevenueValidation::where('station_id', $stationId)
            ->where('date', $data['date'])
            ->where('rotation', $data['rotation'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Cette rotation a déjà été validée pour cette date.');
        }

        // Enregistre dans DailyRevenueValidation
        \App\Models\DailyRevenueValidation::create([
            'station_id' => $stationId,
            'date' => $data['date'],
            'rotation' => $data['rotation'],
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Enregistre dans DailyRevenueReview
        \App\Models\DailyRevenueReview::create([
            'station_id' => $stationId,
            'date' => $data['date'],
            'rotation' => $data['rotation'],
            'fuel_amount' => $data['fuel_amount'] ?? 0,
            'product_amount' => $data['product_amount'] ?? 0,
            'shop_amount' => $data['shop_amount'] ?? 0,
            'tpe_amount' => $data['tpe_amount'] ?? 0,
            'om_amount' => $data['om_amount'] ?? 0,
            'balance_received' => $data['balance_received'] ?? 0,
            'balance_used' => $data['balance_used'] ?? 0,
            'credit_received' => $data['credit_received'] ?? 0,
            'credit_repaid' => $data['credit_repaid'] ?? 0,
            'expenses' => $data['expenses'] ?? 0,
            'net_to_deposit' => $data['net_to_deposit'] ?? 0,
        ]);

        return redirect()->route('daily-revenue-validations.index')
            ->with('success', 'Rotation validée avec succès.');
    }

    public function fetch(Request $request)
    {
        try {
            $stationId = session('selected_station_id');
            $date = $request->get('date');
            $rotation = $request->get('rotation');

            if (! $date || ! $rotation) {
                return response()->json([
                    'message' => 'La date et la rotation sont requises.',
                ], 422);
            }

            $fuel = FuelIndex::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->sum('montant_declare');

            $product = DailyProductSale::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->sum('total_price');

            $shop = DailySimpleRevenue::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->whereIn('type', ['boutique', 'lavage'])
                ->sum('amount');

            $balanceReceived = BalanceTopup::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->sum('amount');

            $balanceUsed = BalanceUsage::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->sum('amount');

            $creditReceived = CreditTopup::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->sum('amount');

            $creditRepaid = CreditPayment::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->sum('amount');

            $expenses = Expense::where('station_id', $stationId)
                ->where('date_depense', $date)
                ->where('rotation', $rotation)
                ->sum('montant');

            return response()->json([
                'fuel_amount' => $fuel,
                'product_amount' => $product,
                'shop_amount' => $shop,
                'balance_received' => $balanceReceived,
                'balance_used' => $balanceUsed,
                'credit_received' => $creditReceived,
                'credit_repaid' => $creditRepaid,
                'expenses' => $expenses,
                'om_amount' => 0,
                'tpe_amount' => 0,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur serveur : '.$e->getMessage(),
            ], 500);
        }
    }
}
