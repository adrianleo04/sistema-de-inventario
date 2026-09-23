<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UnidadMedidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo Organizacional
    Route::resource('empresas', EmpresaController::class);
    Route::resource('sucursales', SucursalController::class);
    Route::resource('areas', AreaController::class);

    // Módulo Catálogo
    Route::resource('categorias', CategoriaController::class)->except(['create', 'edit', 'show']);
    Route::resource('unidades', UnidadMedidaController::class)->except(['create', 'edit', 'show']);
    Route::resource('proveedores', ProveedorController::class)->except(['create', 'edit', 'show']);
    Route::resource('items', ItemController::class);
});

require __DIR__.'/auth.php';
