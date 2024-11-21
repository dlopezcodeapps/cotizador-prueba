<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class CotizadorController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [ ]);
    }

    public function productos()
    {
        return response()->json(Product::where('stock', '>', 0)->get());
    }

    public function clientes()
    {
        return response()->json(Client::all());
    }
}
