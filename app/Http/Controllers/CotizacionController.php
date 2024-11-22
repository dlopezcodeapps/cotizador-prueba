<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\PrecioStock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CotizacionController extends Controller
{
    // Mostrar todas las cotizaciones
    public function index()
    {
        return Inertia::render('Welcome', [
            'cotizaciones' => Cotizacion::with('cliente', 'detalles.producto', 'detalles.precioStock')->get(),
        ]);
    }


    // Mostrar formulario para crear una cotización
    public function create()
    {
        return Inertia::render('Cotizaciones/Create', [
            'clientes' => Cliente::all(),
            'productos' => Producto::with('preciosStocks')->get(),
        ]);
    }

    // Guardar una nueva cotización
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array',
            'productos.*.precio_stock_id' => 'required|exists:precios_stocks,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $productos = collect($request->productos);

        // Validar stocks disponibles
        foreach ($productos as $producto) {
            $precioStock = PrecioStock::find($producto['precio_stock_id']);
            if (!$precioStock || $precioStock->stock < $producto['cantidad']) {
                return back()->withErrors([
                    'error' => 'Stock insuficiente para el producto ' . ($precioStock->producto->nombre ?? 'desconocido'),
                ]);
            }
        }

        // Calcular el total de la cotización
        $total = $productos->reduce(function ($carry, $producto) {
            $precioStock = PrecioStock::find($producto['precio_stock_id']);
            return $carry + ($precioStock->precio * $producto['cantidad']);
        }, 0);

        // Crear la cotización
        $cotizacion = Cotizacion::create([
            'cliente_id' => $request->cliente_id,
            'total' => $total,
        ]);

        // Crear los detalles y ajustar el stock
        foreach ($productos as $producto) {
            $precioStock = PrecioStock::find($producto['precio_stock_id']);
            $precioStock->decrement('stock', $producto['cantidad']);

            $cotizacion->detalles()->create([
                'producto_id' => $precioStock->producto_id,
                'precio_stock_id' => $producto['precio_stock_id'],
                'cantidad' => $producto['cantidad'],
                'subtotal' => $precioStock->precio * $producto['cantidad'],
            ]);
        }

        return redirect()->route('cotizaciones.index')->with('success', 'Cotización creada exitosamente.');
    }
}
