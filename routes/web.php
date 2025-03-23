<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StationUserController;

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
Route::get('suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show')
    ->middleware('can:supplier-view');
Route::get('suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')
    ->middleware('can:supplier-edit');
Route::post('suppliers/{id}/update', [SupplierController::class, 'update'])->name('suppliers.update')
    ->middleware('can:supplier-edit');
Route::post('suppliers/{id}/delete', [SupplierController::class, 'destroy'])->name('suppliers.destroy')
    ->middleware('can:supplier-delete');

// Gestion des clients
Route::get('clients', [ClientController::class, 'index'])->name('clients.index')
    ->middleware('can:client-list');
Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create')
    ->middleware('can:client-create');
Route::post('clients', [ClientController::class, 'store'])->name('clients.store')
    ->middleware('can:client-create');
Route::get('clients/{id}', [ClientController::class, 'show'])->name('clients.show')
    ->middleware('can:client-view');
Route::get('clients/{id}/edit', [ClientController::class, 'edit'])->name('clients.edit')
    ->middleware('can:client-edit');
Route::post('clients/{id}/update', [ClientController::class, 'update'])->name('clients.update')
    ->middleware('can:client-edit');
Route::post('clients/{id}/delete', [ClientController::class, 'destroy'])->name('clients.destroy')
    ->middleware('can:client-delete');

// Gestion des crédits clients (approvisionnement, dettes, remboursements)
Route::post('clients/{id}/credit/add', [ClientController::class, 'addCredit'])->name('clients.credit.add')
    ->middleware('can:client-credit');
Route::post('clients/{id}/credit/debt', [ClientController::class, 'addDebt'])->name('clients.credit.debt')
    ->middleware('can:client-credit');
Route::post('clients/{id}/credit/pay', [ClientController::class, 'payDebt'])->name('clients.credit.pay')
    ->middleware('can:client-credit');


});





require __DIR__.'/auth.php';
