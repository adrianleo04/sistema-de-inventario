<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UnidadMedidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

    // Núcleo de Movimientos de Inventario
    Route::get('/movimientos/stock-disponible', [MovimientoInventarioController::class, 'getStockDisponible'])->name('movimientos.stock');
    Route::resource('movimientos', MovimientoInventarioController::class)->only(['index', 'create', 'store']);

    // Módulo de Reportes
    Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('/reportes/inventario/exportar-excel', [ReporteController::class, 'exportarInventarioExcel'])->name('reportes.inventario.excel');
    Route::get('/reportes/inventario/exportar-pdf', [ReporteController::class, 'exportarInventarioPdf'])->name('reportes.inventario.pdf');

    Route::get('/reportes/movimientos', [ReporteController::class, 'movimientos'])->name('reportes.movimientos');
    Route::get('/reportes/movimientos/exportar-excel', [ReporteController::class, 'exportarMovimientosExcel'])->name('reportes.movimientos.excel');
    Route::get('/reportes/movimientos/exportar-pdf', [ReporteController::class, 'exportarMovimientosPdf'])->name('reportes.movimientos.pdf');
});

require __DIR__.'/auth.php';
