<?php

use App\Http\Controllers\BalanceTopupController;
use App\Http\Controllers\BalanceUsageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientBalanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCreditController;
use App\Http\Controllers\CreditPaymentController;
use App\Http\Controllers\CreditTopupController;
use App\Http\Controllers\DailyProductSaleController;
use App\Http\Controllers\DailyRevenueReviewController;
use App\Http\Controllers\DailyRevenueValidationController;
use App\Http\Controllers\DailySimpleRevenueController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FuelIndexController;
use App\Http\Controllers\FuelReceptionController;
use App\Http\Controllers\LubricantProductController;
use App\Http\Controllers\LubricantReceptionBatchController;
use App\Http\Controllers\LubricantReceptionController;
use App\Http\Controllers\PackagingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPackagingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\StationUserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rôles avec middleware spécifique pour chaque action
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')
        ->middleware('can:role-list');

    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')
        ->middleware('can:role-create');

    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')
        ->middleware('can:role-create');

    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')
        ->middleware('can:role-edit');

    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')
        ->middleware('can:role-edit');

    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')
        ->middleware('can:role-delete');

    // Utilisateurs avec middleware spécifique pour chaque action
    Route::get('users', [UserController::class, 'index'])->name('users.index')
        ->middleware('can:user-list');

    Route::get('users/create', [UserController::class, 'create'])->name('users.create')
        ->middleware('can:user-create');

    Route::post('users', [UserController::class, 'store'])->name('users.store')
        ->middleware('can:user-create');

    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')
        ->middleware('can:user-edit');

    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')
        ->middleware('can:user-edit');

    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')
        ->middleware('can:user-delete');

    // Gestion des stations-service
    Route::get('stations', [StationController::class, 'index'])->name('stations.index')
        ->middleware('can:station-list');
    Route::get('stations/create', [StationController::class, 'create'])->name('stations.create')
        ->middleware('can:station-create');
    Route::post('stations', [StationController::class, 'store'])->name('stations.store')
        ->middleware('can:station-create');

    Route::get('stations/{station}/edit', [StationController::class, 'edit'])->name('stations.edit')
        ->middleware('can:station-edit');

    Route::put('stations/{station}/update', [StationController::class, 'update'])->name('stations.update')
        ->middleware('can:station-edit');

    Route::delete('stations/{station}/delete', [StationController::class, 'destroy'])->name('stations.destroy')
        ->middleware('can:station-delete');

    Route::get('stations/associate', [StationUserController::class, 'index'])->name('stations.associate');
    Route::post('stations/associate', [StationUserController::class, 'store'])->name('stations.associate.store');
    Route::post('stations/detach/{user}', [StationUserController::class, 'detach'])->name('stations.associate.detach');

    // Gestion des crédits clients (approvisionnement, dettes, remboursements)
    Route::post('clients/{id}/credit/add', [ClientController::class, 'addCredit'])->name('clients.credit.add')
        ->middleware('can:client-credit');
    Route::post('clients/{id}/credit/debt', [ClientController::class, 'addDebt'])->name('clients.credit.debt')
        ->middleware('can:client-credit');
    Route::post('clients/{id}/credit/pay', [ClientController::class, 'payDebt'])->name('clients.credit.pay')
        ->middleware('can:client-credit');

});

Route::middleware(['auth', 'ensure.station', 'station.required'])->group(function () {

    // Gestion des catégories de produits
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index')
        ->middleware('can:category-list');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create')
        ->middleware('can:category-create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store')
        ->middleware('can:category-create');

    Route::get('categories/{categorie}/edit', [CategoryController::class, 'edit'])->name('categories.edit')
        ->middleware('can:category-edit');
    Route::put('categories/{categorie}/update', [CategoryController::class, 'update'])->name('categories.update')
        ->middleware('can:category-edit');
    Route::delete('categories/{categorie}/delete', [CategoryController::class, 'destroy'])->name('categories.destroy')
        ->middleware('can:category-delete');

    // Gestion des produits
    Route::get('products', [ProductController::class, 'index'])->name('products.index')
        ->middleware('can:product-list');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create')
        ->middleware('can:product-create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store')
        ->middleware('can:product-create');

    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit')
        ->middleware('can:product-edit');
    Route::put('products/{product}/update', [ProductController::class, 'update'])->name('products.update')
        ->middleware('can:product-edit');
    Route::delete('products/{product}/delete', [ProductController::class, 'destroy'])->name('products.destroy')
        ->middleware('can:product-delete');

    // Gestion des fournisseurs
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index')
        ->middleware('can:supplier-list');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')
        ->middleware('can:supplier-create');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')
        ->middleware('can:supplier-create');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show')
        ->middleware('can:supplier-view');
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')
        ->middleware('can:supplier-edit');
    Route::put('suppliers/{supplier}/update', [SupplierController::class, 'update'])->name('suppliers.update')
        ->middleware('can:supplier-edit');
    Route::delete('suppliers/{supplier}/delete', [SupplierController::class, 'destroy'])->name('suppliers.destroy')
        ->middleware('can:supplier-delete');

    // Gestion des clients
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index')
        ->middleware('can:client-list');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create')
        ->middleware('can:client-create');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store')
        ->middleware('can:client-create');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show')
        ->middleware('can:client-view');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit')
        ->middleware('can:client-edit');
    Route::put('clients/{client}/update', [ClientController::class, 'update'])->name('clients.update')
        ->middleware('can:client-edit');
    Route::delete('clients/{client}/delete', [ClientController::class, 'destroy'])->name('clients.destroy')
        ->middleware('can:client-delete');

    // Gestion des rubriques de dépenses
    Route::get('expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories.index')
        ->middleware('can:expense-category-list');

    Route::get('expense-categories/create', [ExpenseCategoryController::class, 'create'])->name('expense-categories.create')
        ->middleware('can:expense-category-create');

    Route::post('expense-categories', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store')
        ->middleware('can:expense-category-create');

    Route::get('expense-categories/{expense_category}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit')
        ->middleware('can:expense-category-edit');

    Route::put('expense-categories/{expense_category}/update', [ExpenseCategoryController::class, 'update'])->name('expense-categories.update')
        ->middleware('can:expense-category-edit');

    Route::delete('expense-categories/{expense_category}/delete', [ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy')
        ->middleware('can:expense-category-delete');

    Route::resource('tanks', TankController::class);

    Route::resource('fuel-receptions', FuelReceptionController::class);
    Route::get('fuel-receptions/{id}/export', [FuelReceptionController::class, 'export'])->name('fuel-receptions.export');
    Route::get('fuel-receptions/{id}/export-pdf', [FuelReceptionController::class, 'exportPdf'])->name('fuel-receptions.export.pdf');

    Route::resource('expenses', ExpenseController::class);

    Route::resource('packagings', PackagingController::class);

    Route::prefix('lubricant-receptions')->name('lubricant-receptions.')->group(function () {
        Route::get('/batches', [LubricantReceptionBatchController::class, 'index'])->name('batch.index');
        Route::get('/batch/create', [LubricantReceptionBatchController::class, 'create'])->name('batch.create');
        Route::post('/batch/store', [LubricantReceptionBatchController::class, 'store'])->name('batch.store');
        Route::get('/batch/{batch}', [LubricantReceptionBatchController::class, 'show'])->name('batch.show');
        Route::get('/batch/{batch}/edit', [LubricantReceptionBatchController::class, 'edit'])->name('batch.edit');
        Route::put('/batch/{batch}', [LubricantReceptionBatchController::class, 'update'])->name('batch.update'); // ✅ celle-ci
        Route::delete('/batch/{batch}', [LubricantReceptionBatchController::class, 'destroy'])->name('batch.destroy');

        Route::get('/packagings/{productId}', [LubricantReceptionBatchController::class, 'getPackagings'])->name('packagings');
    });

    // Recharges de solde
    Route::resource('balance-topups', BalanceTopupController::class);

    Route::resource('balance-usages', BalanceUsageController::class)->except(['show']);
    Route::get('balances/summary', [ClientBalanceController::class, 'index'])->name('balances.summary');
    Route::get('balances/{client}', [ClientBalanceController::class, 'show'])->name('clients.balance');
    Route::get('clients/{client}/balance-topups', [BalanceTopupController::class, 'byClient'])->name('clients.balance.topups');
    Route::get('clients/{client}/balance-usages', [BalanceUsageController::class, 'byClient'])->name('clients.balance.usages');

    // Recharges de crédit
    Route::resource('credit-topups', CreditTopupController::class);
   // Route::get('/credit-topups/client/{client}', [CreditTopupController::class, 'show'])->name('credit-topups.show');

    Route::get('clients/{client}/credits', [ClientCreditController::class, 'topups'])->name('clients.topups');
    Route::get('clients/{client}/remboursements', [ClientCreditController::class, 'payments'])->name('clients.payments');

    // Remboursements de crédit
    Route::resource('credit-payments', CreditPaymentController::class);

    Route::get('clients/{client}/credit-history/pdf', [ClientController::class, 'exportCreditHistoryPdf'])->name('clients.credit-history.pdf');
    Route::get('clients/{client}/credit-history/excel', [ClientController::class, 'exportCreditHistoryExcel'])->name('clients.credit-history.excel');

    Route::resource('purchase-invoices', PurchaseInvoiceController::class);

    Route::get('purchase-invoices/export/pdf', [PurchaseInvoiceController::class, 'exportPdf'])->name('purchase-invoices.export.pdf');
    Route::get('purchase-invoices/export/excel', [PurchaseInvoiceController::class, 'exportExcel'])->name('purchase-invoices.export.excel');

    Route::prefix('product-packagings')->group(function () {
        Route::get('{product}/', [ProductPackagingController::class, 'index'])->name('product-packagings.index');
        Route::get('{product}/create', [ProductPackagingController::class, 'create'])->name('product-packagings.create');
        Route::post('/', [ProductPackagingController::class, 'store'])->name('product-packagings.store');
        Route::get('product-packagings/{product}/edit/{productPackaging}', [ProductPackagingController::class, 'edit'])
            ->name('product-packagings.edit');

        Route::put('{productPackaging}', [ProductPackagingController::class, 'update'])->name('product-packagings.update');
        Route::delete('{productPackaging}', [ProductPackagingController::class, 'destroy'])->name('product-packagings.destroy');
    });

    Route::resource('lubricant-products', LubricantProductController::class);

    Route::resource('transporters', TransporterController::class);

    Route::resource('drivers', DriverController::class);

    Route::get('/product/{product}/packagings', [LubricantReceptionController::class, 'getPackagings']);

    Route::resource('pumps', PumpController::class);

    Route::resource('fuel-indexes', FuelIndexController::class);

    Route::get('fuel-indexes/details/{date}/{rotation}', [FuelIndexController::class, 'details'])->name('fuel-indexes.details');

    Route::get('fuel-indexes/{fuelIndex}/edit', [FuelIndexController::class, 'edit'])->name('fuel-indexes.edit');
    Route::put('fuel-indexes/{fuelIndex}', [FuelIndexController::class, 'update'])->name('fuel-indexes.update');

    Route::prefix('daily-product-sales')->name('daily-product-sales.')->group(function () {
        Route::get('/', [DailyProductSaleController::class, 'index'])->name('index');
        Route::get('/create', [DailyProductSaleController::class, 'create'])->name('create');
        Route::post('/', [DailyProductSaleController::class, 'store'])->name('store');

        // Route pour afficher les détails par date et rotation
        Route::get('/{date}/{rotation}', [DailyProductSaleController::class, 'show'])->name('show');
    });

    Route::prefix('recettes-simples')->name('daily-simple-revenues.')->group(function () {
        Route::get('/', [DailySimpleRevenueController::class, 'index'])->name('index');
        Route::get('/create', [DailySimpleRevenueController::class, 'create'])->name('create');
        Route::post('/', [DailySimpleRevenueController::class, 'store'])->name('store');
        Route::get('/{dailySimpleRevenue}/edit', [DailySimpleRevenueController::class, 'edit'])->name('edit');
        Route::put('/{dailySimpleRevenue}', [DailySimpleRevenueController::class, 'update'])->name('update');
        Route::delete('/{dailySimpleRevenue}', [DailySimpleRevenueController::class, 'destroy'])->name('destroy');
    });

    Route::resource('daily-revenue-review', DailyRevenueReviewController::class)->only([
        'index', 'create', 'store', 'show',
    ]);

    Route::resource('daily-revenue-validations', DailyRevenueValidationController::class)->only([
        'index', 'create', 'store', 'show'
    ]);

    Route::get('/daily-revenue-validations/fetch', [App\Http\Controllers\DailyRevenueValidationController::class, 'fetch'])->name('daily-revenue-validations.fetch');

});

require __DIR__.'/auth.php';
