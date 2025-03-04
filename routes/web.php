<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

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




});





require __DIR__.'/auth.php';
