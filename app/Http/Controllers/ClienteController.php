<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    // Crear un nuevo cliente
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Crear el cliente
        Cliente::create($request->all());

        // Redirigir con mensaje de éxito
        return redirect()->back()->with('success', 'Cliente agregado exitosamente.');
    }
}
