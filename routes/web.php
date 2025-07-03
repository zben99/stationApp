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
  //  Route::post('stations/detach/{user}', [StationUserController::class, 'detach'])->name('stations.associate.detach');
Route::post('stations/detach', [StationUserController::class, 'detach'])->name('stations.associate.detach');

    // Gestion des crédits clients (approvisionnement, dettes, remboursements)
    Route::post('clients/{id}/credit/add', [ClientController::class, 'addCredit'])->name('clients.credit.add')
        ->middleware('can:client-credit');
    Route::post('clients/{id}/credit/debt', [ClientController::class, 'addDebt'])->name('clients.credit.debt')
        ->middleware('can:client-credit');
    Route::post('clients/{id}/credit/pay', [ClientController::class, 'payDebt'])->name('clients.credit.pay')
        ->middleware('can:client-credit');

});

Route::middleware(['auth', 'ensure.station', 'station.required'])->group(function () {

    // --- CATEGORIES PRODUITS ---
    Route::resource('categories', CategoryController::class)->except('show')->middleware([
        'index' => 'can:category-list',
        'create' => 'can:category-create',
        'store' => 'can:category-create',
        'edit' => 'can:category-edit',
        'update' => 'can:category-edit',
        'destroy' => 'can:category-delete',
    ]);

    // --- PRODUITS ---
    Route::resource('products', ProductController::class)->except('show')->middleware([
        'index' => 'can:product-list',
        'create' => 'can:product-create',
        'store' => 'can:product-create',
        'edit' => 'can:product-edit',
        'update' => 'can:product-edit',
        'destroy' => 'can:product-delete',
    ]);

    // --- FOURNISSEURS ---
    Route::resource('suppliers', SupplierController::class)->middleware([
        'index' => 'can:supplier-list',
        'create' => 'can:supplier-create',
        'store' => 'can:supplier-create',
        'show' => 'can:supplier-view',
        'edit' => 'can:supplier-edit',
        'update' => 'can:supplier-edit',
        'destroy' => 'can:supplier-delete',
    ]);

    // --- CLIENTS ---
    Route::resource('clients', ClientController::class)->middleware([
        'index' => 'can:client-list',
        'create' => 'can:client-create',
        'store' => 'can:client-create',
        'show' => 'can:client-view',
        'edit' => 'can:client-edit',
        'update' => 'can:client-edit',
        'destroy' => 'can:client-delete',
    ]);

    // --- CATEGORIES DE DEPENSES ---
    Route::resource('expense-categories', ExpenseCategoryController::class)->except('show')->middleware([
        'index' => 'can:expense-category-list',
        'create' => 'can:expense-category-create',
        'store' => 'can:expense-category-create',
        'edit' => 'can:expense-category-edit',
        'update' => 'can:expense-category-edit',
        'destroy' => 'can:expense-category-delete',
    ]);

    // --- TANKS ---
    Route::resource('tanks', TankController::class)->middleware([
        'index' => 'can:tank-list',
        'create' => 'can:tank-create',
        'store' => 'can:tank-create',
        'show' => 'can:tank-view',
        'edit' => 'can:tank-edit',
        'update' => 'can:tank-edit',
        'destroy' => 'can:tank-delete',
    ]);

    // --- FUEL RECEPTIONS ---
    Route::resource('fuel-receptions', FuelReceptionController::class)->middleware([
        'index' => 'can:fuel-reception-list',
        'create' => 'can:fuel-reception-create',
        'store' => 'can:fuel-reception-create',
        'show' => 'can:fuel-reception-view',
        'edit' => 'can:fuel-reception-edit',
        'update' => 'can:fuel-reception-edit',
        'destroy' => 'can:fuel-reception-delete',
    ]);
    Route::get('fuel-receptions/{id}/export', [FuelReceptionController::class, 'export'])->name('fuel-receptions.export')->middleware('can:fuel-reception-export');
    Route::get('fuel-receptions/{id}/export-pdf', [FuelReceptionController::class, 'exportPdf'])->name('fuel-receptions.export.pdf')->middleware('can:fuel-reception-export');

    // --- DEPENSES ---
    Route::resource('expenses', ExpenseController::class)->middleware([
        'index' => 'can:expense-list',
        'create' => 'can:expense-create',
        'store' => 'can:expense-create',
        'show' => 'can:expense-view',
        'edit' => 'can:expense-edit',
        'update' => 'can:expense-edit',
        'destroy' => 'can:expense-delete',
    ]);

    // --- CONDITIONNEMENTS ---
    Route::resource('packagings', PackagingController::class)->middleware([
        'index' => 'can:packaging-list',
        'create' => 'can:packaging-create',
        'store' => 'can:packaging-create',
        'edit' => 'can:packaging-edit',
        'update' => 'can:packaging-edit',
        'destroy' => 'can:packaging-delete',
    ]);

    // --- LUBRICANT RECEPTIONS ---
    Route::prefix('lubricant-receptions')->name('lubricant-receptions.')->group(function () {
        Route::get('/batches', [LubricantReceptionBatchController::class, 'index'])->name('batch.index')->middleware('can:lubricant-reception-list');
        Route::get('/batch/create', [LubricantReceptionBatchController::class, 'create'])->name('batch.create')->middleware('can:lubricant-reception-create');
        Route::post('/batch/store', [LubricantReceptionBatchController::class, 'store'])->name('batch.store')->middleware('can:lubricant-reception-create');
        Route::get('/batch/{batch}', [LubricantReceptionBatchController::class, 'show'])->name('batch.show')->middleware('can:lubricant-reception-view');
        Route::get('/batch/{batch}/edit', [LubricantReceptionBatchController::class, 'edit'])->name('batch.edit')->middleware('can:lubricant-reception-edit');
        Route::put('/batch/{batch}', [LubricantReceptionBatchController::class, 'update'])->name('batch.update')->middleware('can:lubricant-reception-edit');
        Route::delete('/batch/{batch}', [LubricantReceptionBatchController::class, 'destroy'])->name('batch.destroy')->middleware('can:lubricant-reception-delete');
        Route::get('/packagings/{productId}', [LubricantReceptionBatchController::class, 'getPackagings'])->name('packagings')->middleware('can:lubricant-reception-view');
    });

    // --- BALANCES ---
    Route::resource('balance-topups', BalanceTopupController::class)->middleware([
        'index' => 'can:balance-topup-list',
        'create' => 'can:balance-topup-create',
        'store' => 'can:balance-topup-create',
        'show' => 'can:balance-topup-view',
        'edit' => 'can:balance-topup-edit',
        'update' => 'can:balance-topup-edit',
        'destroy' => 'can:balance-topup-delete',
    ]);

    Route::resource('balance-usages', BalanceUsageController::class)->except(['show'])->middleware([
        'index' => 'can:balance-usage-list',
        'create' => 'can:balance-usage-create',
        'store' => 'can:balance-usage-create',
        'edit' => 'can:balance-usage-edit',
        'update' => 'can:balance-usage-edit',
        'destroy' => 'can:balance-usage-delete',
    ]);

    Route::get('balances/summary', [ClientBalanceController::class, 'index'])->name('balances.summary')->middleware('can:balance-view');
    Route::get('balances/{client}', [ClientBalanceController::class, 'show'])->name('clients.balance')->middleware('can:balance-view');
    Route::get('clients/{client}/balance-topups', [BalanceTopupController::class, 'byClient'])->name('clients.balance.topups')->middleware('can:balance-view');
    Route::get('clients/{client}/balance-usages', [BalanceUsageController::class, 'byClient'])->name('clients.balance.usages')->middleware('can:balance-view');

    // --- CREDITS ---
    Route::resource('credit-topups', CreditTopupController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'can:credit-topup-list',
            'create' => 'can:credit-topup-create',
            'store' => 'can:credit-topup-create',
            'edit' => 'can:credit-topup-edit',
            'update' => 'can:credit-topup-edit',
            'destroy' => 'can:credit-topup-delete',
        ]);
    Route::get('/credit-topups/client/{client}', [CreditTopupController::class, 'show'])->name('credit-topups.show')->middleware('can:credit-topup-view');
    Route::get('clients/{client}/credits', [ClientCreditController::class, 'topups'])->name('clients.topups')->middleware('can:credit-view');
    Route::get('clients/{client}/remboursements', [ClientCreditController::class, 'payments'])->name('clients.payments')->middleware('can:credit-view');
    Route::resource('credit-payments', CreditPaymentController::class)->middleware([
        'index' => 'can:credit-payment-list',
        'create' => 'can:credit-payment-create',
        'store' => 'can:credit-payment-create',
        'edit' => 'can:credit-payment-edit',
        'update' => 'can:credit-payment-edit',
        'destroy' => 'can:credit-payment-delete',
    ]);
    Route::get('clients/{client}/credit-history/pdf', [ClientController::class, 'exportCreditHistoryPdf'])->name('clients.credit-history.pdf')->middleware('can:credit-export');
    Route::get('clients/{client}/credit-history/excel', [ClientController::class, 'exportCreditHistoryExcel'])->name('clients.credit-history.excel')->middleware('can:credit-export');

    // --- FACTURES D'ACHAT ---
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)->middleware([
        'index' => 'can:purchase-invoice-list',
        'create' => 'can:purchase-invoice-create',
        'store' => 'can:purchase-invoice-create',
        'edit' => 'can:purchase-invoice-edit',
        'update' => 'can:purchase-invoice-edit',
        'destroy' => 'can:purchase-invoice-delete',
    ]);
    Route::get('purchase-invoices/export/pdf', [PurchaseInvoiceController::class, 'exportPdf'])->name('purchase-invoices.export.pdf')->middleware('can:purchase-invoice-export');
    Route::get('purchase-invoices/export/excel', [PurchaseInvoiceController::class, 'exportExcel'])->name('purchase-invoices.export.excel')->middleware('can:purchase-invoice-export');

    // product-packagings
    Route::get('product-packagings/{product}/', [ProductPackagingController::class, 'index'])
        ->name('product-packagings.index')
        ->middleware('can:view-product-packagings');

    Route::get('product-packagings/{product}/create', [ProductPackagingController::class, 'create'])
        ->name('product-packagings.create')
        ->middleware('can:create-product-packagings');

    Route::post('product-packagings/', [ProductPackagingController::class, 'store'])
        ->name('product-packagings.store')
        ->middleware('can:create-product-packagings');

    Route::get('product-packagings/product-packagings/{product}/edit/{productPackaging}', [ProductPackagingController::class, 'edit'])
        ->name('product-packagings.edit')
        ->middleware('can:edit-product-packagings');

    Route::put('product-packagings/{productPackaging}', [ProductPackagingController::class, 'update'])
        ->name('product-packagings.update')
        ->middleware('can:edit-product-packagings');

    Route::delete('product-packagings/{productPackaging}', [ProductPackagingController::class, 'destroy'])
        ->name('product-packagings.destroy')
        ->middleware('can:delete-product-packagings');

    // lubricant-products

    Route::get('lubricant-products', [LubricantProductController::class, 'index'])
        ->name('lubricant-products.index')
        ->middleware('can:view-lubricant-products');

    Route::get('lubricant-products/create', [LubricantProductController::class, 'create'])
        ->name('lubricant-products.create')
        ->middleware('can:create-lubricant-products');

    Route::post('lubricant-products', [LubricantProductController::class, 'store'])
        ->name('lubricant-products.store')
        ->middleware('can:create-lubricant-products');

    Route::get('lubricant-products/{lubricant_product}', [LubricantProductController::class, 'show'])
        ->name('lubricant-products.show')
        ->middleware('can:view-lubricant-products');

    Route::get('lubricant-products/{lubricant_product}/edit', [LubricantProductController::class, 'edit'])
        ->name('lubricant-products.edit')
        ->middleware('can:edit-lubricant-products');

    Route::put('lubricant-products/{lubricant_product}', [LubricantProductController::class, 'update'])
        ->name('lubricant-products.update')
        ->middleware('can:edit-lubricant-products');

    Route::delete('lubricant-products/{lubricant_product}', [LubricantProductController::class, 'destroy'])
        ->name('lubricant-products.destroy')
        ->middleware('can:delete-lubricant-products');

    // transporters
    Route::get('transporters', [TransporterController::class, 'index'])->name('transporters.index')->middleware('can:view-transporters');
    Route::get('transporters/create', [TransporterController::class, 'create'])->name('transporters.create')->middleware('can:create-transporters');
    Route::post('transporters', [TransporterController::class, 'store'])->name('transporters.store')->middleware('can:create-transporters');
    Route::get('transporters/{transporter}', [TransporterController::class, 'show'])->name('transporters.show')->middleware('can:view-transporters');
    Route::get('transporters/{transporter}/edit', [TransporterController::class, 'edit'])->name('transporters.edit')->middleware('can:edit-transporters');
    Route::put('transporters/{transporter}', [TransporterController::class, 'update'])->name('transporters.update')->middleware('can:edit-transporters');
    Route::delete('transporters/{transporter}', [TransporterController::class, 'destroy'])->name('transporters.destroy')->middleware('can:delete-transporters');

    // transporters

    Route::get('drivers', [DriverController::class, 'index'])->name('drivers.index')->middleware('can:view-drivers');
    Route::get('drivers/create', [DriverController::class, 'create'])->name('drivers.create')->middleware('can:create-drivers');
    Route::post('drivers', [DriverController::class, 'store'])->name('drivers.store')->middleware('can:create-drivers');
    Route::get('drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show')->middleware('can:view-drivers');
    Route::get('drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit')->middleware('can:edit-drivers');
    Route::put('drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update')->middleware('can:edit-drivers');
    Route::delete('drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy')->middleware('can:delete-drivers');

    // Conditionnements d’un produit

    Route::get('/product/{product}/packagings', [LubricantReceptionController::class, 'getPackagings'])
        ->middleware('can:view-product-packagings');

    // pumps

    Route::get('pumps', [PumpController::class, 'index'])->name('pumps.index')->middleware('can:view-pumps');
    Route::get('pumps/create', [PumpController::class, 'create'])->name('pumps.create')->middleware('can:create-pumps');
    Route::post('pumps', [PumpController::class, 'store'])->name('pumps.store')->middleware('can:create-pumps');
    Route::get('pumps/{pump}', [PumpController::class, 'show'])->name('pumps.show')->middleware('can:view-pumps');
    Route::get('pumps/{pump}/edit', [PumpController::class, 'edit'])->name('pumps.edit')->middleware('can:edit-pumps');
    Route::put('pumps/{pump}', [PumpController::class, 'update'])->name('pumps.update')->middleware('can:edit-pumps');
    Route::delete('pumps/{pump}', [PumpController::class, 'destroy'])->name('pumps.destroy')->middleware('can:delete-pumps');

    // fuel-indexes

    Route::get('fuel-indexes', [FuelIndexController::class, 'index'])->name('fuel-indexes.index')->middleware('can:view-fuel-indexes');
    Route::get('fuel-indexes/create', [FuelIndexController::class, 'create'])->name('fuel-indexes.create')->middleware('can:create-fuel-indexes');
    Route::post('fuel-indexes', [FuelIndexController::class, 'store'])->name('fuel-indexes.store')->middleware('can:create-fuel-indexes');
    Route::get('fuel-indexes/{fuelIndex}', [FuelIndexController::class, 'show'])->name('fuel-indexes.show')->middleware('can:view-fuel-indexes');
    Route::get('fuel-indexes/{fuelIndex}/edit', [FuelIndexController::class, 'edit'])->name('fuel-indexes.edit')->middleware('can:edit-fuel-indexes');
    Route::put('fuel-indexes/{fuelIndex}', [FuelIndexController::class, 'update'])->name('fuel-indexes.update')->middleware('can:edit-fuel-indexes');

    Route::get('fuel-indexes/details/{date}/{rotation}', [FuelIndexController::class, 'details'])
        ->name('fuel-indexes.details')
        ->middleware('can:view-fuel-index-details');

    // daily-product-sales

    Route::get('daily-product-sales', [DailyProductSaleController::class, 'index'])->name('daily-product-sales.index')->middleware('can:view-daily-product-sales');
    Route::get('daily-product-sales/create', [DailyProductSaleController::class, 'create'])->name('daily-product-sales.create')->middleware('can:create-daily-product-sales');
    Route::post('daily-product-sales', [DailyProductSaleController::class, 'store'])->name('daily-product-sales.store')->middleware('can:create-daily-product-sales');
    Route::get('daily-product-sales/{date}/{rotation}', [DailyProductSaleController::class, 'show'])->name('daily-product-sales.show')->middleware('can:view-daily-product-sales');

    // recettes-simples
    Route::get('recettes-simples', [DailySimpleRevenueController::class, 'index'])->name('daily-simple-revenues.index')->middleware('can:view-daily-simple-revenues');
    Route::get('recettes-simples/create', [DailySimpleRevenueController::class, 'create'])->name('daily-simple-revenues.create')->middleware('can:create-daily-simple-revenues');
    Route::post('recettes-simples', [DailySimpleRevenueController::class, 'store'])->name('daily-simple-revenues.store')->middleware('can:create-daily-simple-revenues');
    Route::get('recettes-simples/{dailySimpleRevenue}/edit', [DailySimpleRevenueController::class, 'edit'])->name('daily-simple-revenues.edit')->middleware('can:edit-daily-simple-revenues');
    Route::put('recettes-simples/{dailySimpleRevenue}', [DailySimpleRevenueController::class, 'update'])->name('daily-simple-revenues.update')->middleware('can:edit-daily-simple-revenues');
    Route::delete('recettes-simples/{dailySimpleRevenue}', [DailySimpleRevenueController::class, 'destroy'])->name('daily-simple-revenues.destroy')->middleware('can:delete-daily-simple-revenues');

    // daily-revenue-review
    Route::get('daily-revenue-review', [DailyRevenueReviewController::class, 'index'])->name('daily-revenue-review.index')->middleware('can:view-daily-revenue-review');
    Route::get('daily-revenue-review/create', [DailyRevenueReviewController::class, 'create'])->name('daily-revenue-review.create')->middleware('can:create-daily-revenue-review');
    Route::post('daily-revenue-review', [DailyRevenueReviewController::class, 'store'])->name('daily-revenue-review.store')->middleware('can:create-daily-revenue-review');
    Route::get('daily-revenue-review/{dailyRevenueReview}', [DailyRevenueReviewController::class, 'show'])->name('daily-revenue-review.show')->middleware('can:view-daily-revenue-review');

    // daily-revenue-validations

    Route::get('daily-revenue-validations/fetch', [DailyRevenueValidationController::class, 'fetch'])
        ->name('daily-revenue-validations.fetch')
        ->middleware('can:view-daily-revenue-validations');

    Route::get('daily-revenue-validations', [DailyRevenueValidationController::class, 'index'])
        ->name('daily-revenue-validations.index')
        ->middleware('can:view-daily-revenue-validations');

    Route::get('daily-revenue-validations/create', [DailyRevenueValidationController::class, 'create'])
        ->name('daily-revenue-validations.create')
        ->middleware('can:create-daily-revenue-validations');

    Route::post('daily-revenue-validations', [DailyRevenueValidationController::class, 'store'])
        ->name('daily-revenue-validations.store')
        ->middleware('can:create-daily-revenue-validations');

    Route::get('daily-revenue-validations/{dailyRevenueValidation}', [DailyRevenueValidationController::class, 'show'])
        ->name('daily-revenue-validations.show')
        ->middleware('can:view-daily-revenue-validations');

});

require __DIR__.'/auth.php';
