<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SucursalController;
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
});

require __DIR__.'/auth.php';
