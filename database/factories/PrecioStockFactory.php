<?php

namespace Database\Factories;

use App\Models\PrecioStock;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrecioStockFactory extends Factory
{
    protected $model = PrecioStock::class;

    public function definition()
    {
        return [
            'producto_id' => Producto::factory(), // Crea un producto asociado
            'precio' => $this->faker->randomFloat(2, 10, 500), // Precio entre 10 y 500
            'stock' => $this->faker->numberBetween(1, 100), // Stock entre 1 y 100
        ];
    }
}