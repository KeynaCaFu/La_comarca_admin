<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome page (without authentication)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Redirect legacy routes to login
Route::get('/entrar/admin/local', function () {
    return redirect()->route('login');
})->name('enter.local');

Route::get('/entrar/admin/global', function () {
    return redirect()->route('login');
})->name('enter.global');

// Dashboard - requires authentication
Route::get('/dashboard', function () {
    if (auth()->user()->isAdminGlobal()) {
        return redirect()->route('eventos.index');
    }
    // For local managers, show the dashboard
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============================================================================
// RUTAS PARA ADMIN GLOBAL (Administrador Principal)
// ============================================================================
Route::middleware(['auth', 'verified', 'admin.global'])->group(function () {
    // Eventos
    Route::prefix('eventos')->name('eventos.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{id}', [EventController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EventController::class, 'update'])->name('update');
        Route::delete('/{id}', [EventController::class, 'destroy'])->name('destroy');
    });

    // Productos
    Route::prefix('productos')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Proveedores
    Route::prefix('proveedores')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
    });
});

// ============================================================================
// RUTAS PARA ADMIN LOCAL (Gerentes)
// ============================================================================
Route::middleware(['auth', 'verified', 'admin.local'])->group(function () {
    // Insumos (Supplies)
    Route::prefix('insumos')->name('supplies.')->group(function () {
        Route::get('/', [SupplyController::class, 'index'])->name('index');
        Route::get('/create', [SupplyController::class, 'create'])->name('create');
        Route::post('/', [SupplyController::class, 'store'])->name('store');
        Route::get('/{id}', [SupplyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SupplyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SupplyController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupplyController::class, 'destroy'])->name('destroy');
    });
});

// Profile routes (authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
