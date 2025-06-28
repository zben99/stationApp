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
use App\Models\StationCategory;
use App\Models\StationProduct;
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

        return view('daily_revenue_validations.create');
    }

    public function store(Request $request)
    {
        $stationId = session('selected_station_id');

        /* -------- 1. VALIDATION ------------------------------------ */
        $data = $request->validate([
            'date' => 'required|date',
            'rotation' => 'required|in:6-14,14-22,22-6',

            /* Carburants */
            'fuel_super_amount' => 'nullable|numeric|min:0',
            'fuel_gazoil_amount' => 'nullable|numeric|min:0',

            /* Produits par famille */
            'lub_amount' => 'nullable|numeric|min:0',
            'pea_amount' => 'nullable|numeric|min:0',
            'gaz_amount' => 'nullable|numeric|min:0',
            'lampes_amount' => 'nullable|numeric|min:0',
            'lavage_amount' => 'nullable|numeric|min:0',
            'boutique_amount' => 'nullable|numeric|min:0',

            /* Crédits / Avoirs */
            'credit_received' => 'nullable|numeric|min:0',
            'credit_repaid' => 'nullable|numeric|min:0',
            'balance_received' => 'nullable|numeric|min:0',
            'balance_used' => 'nullable|numeric|min:0',

            /* Électronique */
            'tpe_amount' => 'nullable|numeric|min:0',
            'tpe_recharge_amount' => 'nullable|numeric|min:0',
            'om_recharge_amount' => 'nullable|numeric|min:0',
            'om_amount' => 'nullable|numeric|min:0',

            /* Dépenses & caisse */
            'expenses' => 'nullable|numeric|min:0',
            'cash_amount' => 'nullable|numeric|min:0',
            'net_to_deposit' => 'nullable|numeric|min:0',
        ]);

        /* -------- 2. UNICITÉ (station + date + rotation) ---------- */
        $exists = DailyRevenueValidation::where('station_id', $stationId)
            ->where('date', $data['date'])
            ->where('rotation', $data['rotation'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Cette rotation est déjà validée pour cette date.');
        }

        /* -------- 3. CALCUL DU NET (sécurité côté back) ----------- */
        $totalIn = (
            ($data['fuel_super_amount'] ?? 0)
        + ($data['fuel_gazoil_amount'] ?? 0)
        + ($data['lub_amount'] ?? 0)
        + ($data['pea_amount'] ?? 0)
        + ($data['gaz_amount'] ?? 0)
        + ($data['lampes_amount'] ?? 0)
        + ($data['lavage_amount'] ?? 0)
        + ($data['boutique_amount'] ?? 0)
        + ($data['credit_repaid'] ?? 0)
        + ($data['balance_received'] ?? 0)
        + ($data['tpe_recharge_amount'] ?? 0)
        + ($data['om_recharge_amount'] ?? 0)
        );

        $totalOut = (
            ($data['expenses'] ?? 0)
        + ($data['credit_received'] ?? 0)
        + ($data['balance_used'] ?? 0)
        + ($data['tpe_amount'] ?? 0)
        + ($data['om_amount'] ?? 0)
        );

        $net = round($totalIn - $totalOut, 2);

        /* -------- 4. INSERTION ------------------------------------ */
        DailyRevenueValidation::create([
            'station_id' => $stationId,
            'date' => $data['date'],
            'rotation' => $data['rotation'],

            'fuel_super_amount' => $data['fuel_super_amount'] ?? 0,
            'fuel_gazoil_amount' => $data['fuel_gazoil_amount'] ?? 0,

            'lub_amount' => $data['lub_amount'] ?? 0,
            'pea_amount' => $data['pea_amount'] ?? 0,
            'gaz_amount' => $data['gaz_amount'] ?? 0,
            'lampes_amount' => $data['lampes_amount'] ?? 0,
            'lavage_amount' => $data['lavage_amount'] ?? 0,
            'boutique_amount' => $data['boutique_amount'] ?? 0,

            'credit_received' => $data['credit_received'] ?? 0,
            'credit_repaid' => $data['credit_repaid'] ?? 0,
            'balance_received' => $data['balance_received'] ?? 0,
            'balance_used' => $data['balance_used'] ?? 0,

            'tpe_amount' => $data['tpe_amount'] ?? 0,
            'tpe_recharge_amount' => $data['tpe_recharge_amount'] ?? 0,
            'om_recharge_amount' => $data['om_recharge_amount'] ?? 0,
            'om_amount' => $data['om_amount'] ?? 0,

            'expenses' => $data['expenses'] ?? 0,
            'cash_amount' => $data['cash_amount'] ?? 0,
            'net_to_deposit' => $net,                       // override pour cohérence

            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return redirect()
            ->route('daily-revenue-validations.index')
            ->with('success', 'Rotation validée avec succès.');
    }

    public function show(DailyRevenueValidation $dailyRevenueValidation)
    {
        return view('daily_revenue_validations.show', [
            'validation' => $dailyRevenueValidation,
        ]);
    }

    public function fetch(Request $request)
    {
        try {
            /* -----------------------------------------------------------------
            | 1. Paramètres et validation
            * -----------------------------------------------------------------*/
            $stationId = session('selected_station_id');
            $date = $request->get('date');
            $rotation = $request->get('rotation');

            if (! $date || ! $rotation) {
                return response()->json([
                    'message' => 'La date et la rotation sont requises.',
                ], 422);
            }

            /* -----------------------------------------------------------------
            | 2. Récupération des IDs PRODUITS : Super & Gazoil
            |    (on filtre par nom ; adapte si tu utilises un champ `code` ou `slug`)
            * -----------------------------------------------------------------*/
            $superId = StationProduct::where('station_id', $stationId)
                ->where('name', 'like', '%Super%')
                ->value('id');

            $gazoilId = StationProduct::where('station_id', $stationId)
                ->where(function ($q) {
                    $q->where('name', 'like', '%GASOIL%');
                })
                ->value('id');

            /* -----------------------------------------------------------------
            | 3. Sommes recettes FUEL : Super & Gazoil séparés
            |    Si FuelIndex possède directement un `product_id`, garde where().
            |    Ici on passe par pump ➜ tank ➜ product.
            * -----------------------------------------------------------------*/
            $fuelBase = FuelIndex::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation);

            $fuelSuper = $superId
                ? (clone $fuelBase)
                    ->whereHas('pump.tank.product', fn ($q) => $q->where('id', $superId))
                    ->sum('montant_recette')
                : 0;

            $fuelGazoil = $gazoilId
                ? (clone $fuelBase)
                    ->whereHas('pump.tank.product', fn ($q) => $q->where('id', $gazoilId))
                    ->sum('montant_recette')
                : 0;

            /* -----------------------------------------------------------------
            | 4. IDs catégories : lubrifiant, pea, gaz, lampe
            * -----------------------------------------------------------------*/
            $catIds = StationCategory::where('station_id', $stationId)
                ->whereIn('name', ['Lubrifiant', 'Produits d\'Entretien Auto (PEA)', 'GAZ', 'Lampes'])
                ->pluck('id', 'name'); // ['lubrifiant' => 3, …]

            /* -----------------------------------------------------------------
            | 5. Sommes ventes PRODUITS par catégorie
            * -----------------------------------------------------------------*/
            $productBase = DailyProductSale::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation);

            $lub = isset($catIds['Lubrifiant'])
                ? (clone $productBase)
                    ->whereHas('productPackaging.product', fn ($q) => $q->where('category_id', $catIds['Lubrifiant']))
                    ->sum('total_price')
                : 0;

            $pea = isset($catIds['Produits d\'Entretien Auto (PEA)'])
                ? (clone $productBase)
                    ->whereHas('productPackaging.product', fn ($q) => $q->where('category_id', $catIds['Produits d\'Entretien Auto (PEA)']))
                    ->sum('total_price')
                : 0;

            $gaz = isset($catIds['GAZ'])
                ? (clone $productBase)
                    ->whereHas('productPackaging.product', fn ($q) => $q->where('category_id', $catIds['GAZ']))
                    ->sum('total_price')
                : 0;

            $lampes = isset($catIds['Lampes'])
                ? (clone $productBase)
                    ->whereHas('productPackaging.product', fn ($q) => $q->where('category_id', $catIds['Lampes']))
                    ->sum('total_price')
                : 0;

            /* -----------------------------------------------------------------
            | 6. Boutique & Lavage (DailySimpleRevenue)
            * -----------------------------------------------------------------*/
            $lavage = DailySimpleRevenue::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->where('type', 'lavage')
                ->sum('amount');

            $boutique = DailySimpleRevenue::where('station_id', $stationId)
                ->where('date', $date)
                ->where('rotation', $rotation)
                ->where('type', 'boutique')
                ->sum('amount');

            /* -----------------------------------------------------------------
            | 7. Soldes, crédits, dépenses
            * -----------------------------------------------------------------*/
            $balanceReceived = BalanceTopup::where('station_id', $stationId)
                ->where('date', $date)->where('rotation', $rotation)->sum('amount');

            $balanceUsed = BalanceUsage::where('station_id', $stationId)
                ->where('date', $date)->where('rotation', $rotation)->sum('amount');

            $creditReceived = CreditTopup::where('station_id', $stationId)
                ->where('date', $date)->where('rotation', $rotation)->sum('amount');

            $creditRepaid = CreditPayment::where('station_id', $stationId)
                ->where('date', $date)->where('rotation', $rotation)->sum('amount');

            $expenses = Expense::where('station_id', $stationId)
                ->where('date_depense', $date)->where('rotation', $rotation)->sum('montant');

            /* -----------------------------------------------------------------
            | 8. Réponse JSON
            * -----------------------------------------------------------------*/
            return response()->json([
                // carburants
                'fuel_super_amount' => $fuelSuper,
                'fuel_gazoil_amount' => $fuelGazoil,
                'fuel_amount' => $fuelSuper + $fuelGazoil,

                // autres produits
                'lub_amount' => $lub,
                'pea_amount' => $pea,
                'gaz_amount' => $gaz,
                'lampes_amount' => $lampes,
                'lavage_amount' => $lavage,
                'boutique_amount' => $boutique,

                // soldes / crédits / dépenses
                'balance_received' => $balanceReceived,
                'balance_used' => $balanceUsed,
                'credit_received' => $creditReceived,
                'credit_repaid' => $creditRepaid,
                'expenses' => $expenses,

                // placeholders (mobile money / TPE)
                'om_amount' => 0,
                'tpe_amount' => 0,
                'tpe_recharge_amount' => 0,
                'om_recharge_amount' => 0,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur serveur : '.$e->getMessage(),
            ], 500);
        }
    }
}
