<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PuntoVentaController;

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

Route::get('/', [GeneralController::class, 'index'])->name('login');

Route::get('/dashboard', [GeneralController::class, 'dashboard_index'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

// Productos
Route::resource('productos', ProductsController::class)->except(['show','create'])->middleware(['auth', 'verified'])->names('products');
Route::delete('productos/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');
Route::get('productos/all', [ProductsController::class,'getAll'])->name('products.all');
// Punto de venta
Route::get('punto-venta', [PuntoVentaController::class, 'index'])->name('punto_venta.index');
// Acerca de nosotros
Route::get('acerca-de-nosotros', [GeneralController::class, 'about'])->name('about.index');