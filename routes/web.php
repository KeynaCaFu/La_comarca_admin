<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;

// ============================================================================
// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Ruta del dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Rutas rápidas para entrar como admin local/global (guardan modo en sesión)
Route::get('/entrar/admin/local', function (Request $request) {
    $request->session()->put('admin_mode', 'local');
    return redirect()->route('dashboard');
})->name('enter.local');

Route::get('/entrar/admin/global', function (Request $request) {
    $request->session()->put('admin_mode', 'global');
    return redirect()->route('eventos.index');
})->name('enter.global');

// ============================================================================

Route::prefix('insumos')->name('supplies.')->group(function () {
    // Lista de supplies
    Route::get('/', [SupplyController::class, 'index'])->name('index');

    // Formulario crear supply
    Route::get('/create', [SupplyController::class, 'create'])->name('create');
    
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

Route::prefix('eventos')->name('eventos.')->group(function () {
    // Lista de eventos
    Route::get('/', [EventController::class, 'index'])->name('index');
    // Guardar evento (vista crea usa 'eventos.guardar')
    Route::post('/', [EventController::class, 'store'])->name('guardar');

    // Editar evento (vista usa 'eventos.editar')
    Route::get('/{evento}/edit', [EventController::class, 'edit'])->name('editar');

    // Modales (AJAX) para eventos
    Route::get('/{id}/show-modal', [EventController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [EventController::class, 'editModal'])->name('edit.modal');

    // Actualizar evento (vista usa 'eventos.actualizar')
    Route::put('/{evento}', [EventController::class, 'update'])->name('actualizar');

    // Eliminar evento (vista usa 'eventos.eliminar')
    Route::delete('/{evento}', [EventController::class, 'destroy'])->name('eliminar');
});



Route::prefix('proveedores')->name('suppliers.')->group(function () {
    // Lista de suppliers
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    
    // Restaurar supplier (URL firmada por 10s) con token de restauración
    Route::get('/restore/{token}', [SupplierController::class, 'restore'])
        ->name('restore')
        ->middleware('signed');

    // Verificar email duplicado
    Route::post('/check-email', [SupplierController::class, 'checkEmail'])->name('check.email');
    
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

