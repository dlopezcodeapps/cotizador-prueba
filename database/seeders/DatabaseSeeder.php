<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // Crear 10 productos, cada uno con 3 precios/stocks
       Producto::factory()
       ->count(10) // Crear 10 productos
       ->withPreciosStocks(3) // Cada producto tiene 3 precios/stocks
       ->create();
    }
}
