<?php

use App\Http\Controllers\CotizadorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [CotizadorController::class, 'index'])->name('cotizador.inicio');
Route::get('/productos', [CotizadorController::class, 'productos'])->name('cotizador.productos');
Route::get('/clientes', [CotizadorController::class, 'clientes'])->name('cotizador.clientes');
