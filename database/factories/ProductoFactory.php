<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\PrecioStock;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->word(),
        ];
    }

    // Define un estado para crear productos con precios y stocks
    public function withPreciosStocks(int $cantidad = 2)
    {
        return $this->afterCreating(function (Producto $producto) use ($cantidad) {
            PrecioStock::factory()
                ->count($cantidad) // Cantidad de precios y stocks por producto
                ->create(['producto_id' => $producto->id]);
        });
    }
}
