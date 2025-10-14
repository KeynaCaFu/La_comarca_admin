<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\ProveedorController;



// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Ruta del dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Rutas de Insumos
Route::prefix('insumos')->name('insumos.')->group(function () {
    Route::get('/', [InsumoController::class, 'index'])->name('index');
    Route::post('/', [InsumoController::class, 'store'])->name('store');
    Route::get('/{id}', [InsumoController::class, 'show'])->name('show');
    Route::put('/{id}', [InsumoController::class, 'update'])->name('update');
    Route::delete('/{id}', [InsumoController::class, 'destroy'])->name('destroy');
    
    // Rutas para modales
    Route::get('/{id}/show-modal', [InsumoController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [InsumoController::class, 'editModal'])->name('edit.modal');
});

// Rutas de Proveedores
Route::prefix('proveedores')->name('proveedores.')->group(function () {
    Route::get('/', [ProveedorController::class, 'index'])->name('index');
    Route::post('/', [ProveedorController::class, 'store'])->name('store');
    Route::get('/{id}', [ProveedorController::class, 'show'])->name('show');
    Route::put('/{id}', [ProveedorController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProveedorController::class, 'destroy'])->name('destroy');
    
    // Rutas para modales
    Route::get('/{id}/show-modal', [ProveedorController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [ProveedorController::class, 'editModal'])->name('edit.modal');
});
