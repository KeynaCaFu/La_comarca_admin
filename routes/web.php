<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
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

// Rutas rápidas para entrar como admin local/global 
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
    // Lista de Insumos
    Route::get('/', [SupplyController::class, 'index'])->name('index');

    // Formulario crear Insumo
    Route::get('/create', [SupplyController::class, 'create'])->name('create');
    
    // Crear Insumo
    Route::post('/', [SupplyController::class, 'store'])->name('store');
    
    // Ver Insumo
    Route::get('/{id}', [SupplyController::class, 'show'])->name('show');
    
    // Actualizar Insumo
    Route::put('/{id}', [SupplyController::class, 'update'])->name('update');
    
    // Eliminar Insumo
    Route::delete('/{id}', [SupplyController::class, 'destroy'])->name('destroy');
    
    // Modales (AJAX)
    Route::get('/{id}/show-modal', [SupplyController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [SupplyController::class, 'editModal'])->name('edit.modal');
});

// ============================================================================

Route::prefix('eventos')->name('eventos.')->group(function () {
    // Lista de eventos
    Route::get('/', [EventController::class, 'index'])->name('index');
    // Guardar evento 
    Route::post('/', [EventController::class, 'store'])->name('guardar');

    // Editar evento
    Route::get('/{evento}/edit', [EventController::class, 'edit'])->name('editar');

    // Modales (AJAX) para eventos
    Route::get('/{event_id}/show-modal', [EventController::class, 'showModal'])->name('show.modal');
    Route::get('/{event_id}/edit-modal', [EventController::class, 'editModal'])->name('edit.modal');

    // Actualizar evento
    Route::put('/{evento}', [EventController::class, 'update'])->name('actualizar');

    // Eliminar evento 
    Route::delete('/{evento}', [EventController::class, 'destroy'])->name('eliminar');
});



Route::prefix('proveedores')->name('suppliers.')->group(function () {
    // Lista de proveedores
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    
    // Restaurar proveedor (URL firmada por 10s) con token de restauración
    Route::get('/restore/{token}', [SupplierController::class, 'restore'])
        ->name('restore')
        ->middleware('signed');

    // Verificar email duplicado
    Route::post('/check-email', [SupplierController::class, 'checkEmail'])->name('check.email');
    
    // Crear proveedores
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    
    // Ver proveedor
    Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
    
    // Actualizar proveedor
    Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
    
    // Eliminar proveedor
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
    
    // Modales (AJAX)
    Route::get('/{id}/show-modal', [SupplierController::class, 'showModal'])->name('show.modal');
    Route::get('/{id}/edit-modal', [SupplierController::class, 'editModal'])->name('edit.modal');
});

// ============================================================================

Route::prefix('productos')->name('products.')->group(function () {
    // Lista de productos
    Route::get('/', [ProductController::class, 'index'])->name('index');
    
    // Formulario crear producto
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    
    // Crear producto
    Route::post('/', [ProductController::class, 'store'])->name('store');
    
    // Ver producto
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    
    // Formulario editar producto
    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
    
    // Actualizar producto
    Route::put('/{id}', [ProductController::class, 'update'])->name('update');
    
    // Eliminar producto
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    
    // Galería de imágenes
    Route::get('/{id}/gallery', [ProductController::class, 'gallery'])->name('gallery');
    Route::post('/{id}/gallery/add', [ProductController::class, 'addGalleryImage'])->name('gallery.add');
    Route::delete('/gallery/{galleryId}', [ProductController::class, 'removeGalleryImage'])->name('gallery.remove');
});

