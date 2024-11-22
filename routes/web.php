<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CotizacionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Ruta principal redirigida a cotizaciones
Route::get('/', [CotizacionController::class, 'index'])->name('home');

// Rutas de cotizaciones
Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('cotizaciones.index');
Route::get('/cotizaciones/create', [CotizacionController::class, 'create'])->name('cotizaciones.create');
Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizaciones.store');

// Rutas de clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
