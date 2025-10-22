<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplierController;

// ============================================================================
// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Ruta del dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// ============================================================================

Route::prefix('insumos')->name('supplies.')->group(function () {
    // Lista de supplies
    Route::get('/', [SupplyController::class, 'index'])->name('index');
    
    // Crear supply
    Route::post('/', [SupplyController::class, 'store'])->name('store');
    
    // Ver supply
    Route::get('/{id}', [SupplyController::class, 'show'])->name('show');
    
    // Actualizar supply
    Route::put('/{id}', [SupplyController::class, 'update'])->name('update');
    
    // Eliminar supply
    Route::delete('/{id}', [SupplyController::class, 'destroy'])->name('destroy');
    
    // Modales (AJAX)
    Route::get('/{id}/show-modal', [SupplyController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [SupplyController::class, 'editModal'])->name('edit.modal');
});

// ============================================================================


Route::prefix('proveedores')->name('suppliers.')->group(function () {
    // Lista de suppliers
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    
    // Crear supplier
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    
    // Ver supplier
    Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
    
    // Actualizar supplier
    Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
    
    // Eliminar supplier
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
    
    // Modales (AJAX)
    Route::get('/{id}/show-modal', [SupplierController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [SupplierController::class, 'editModal'])->name('edit.modal');
});

