<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];
    protected $table = 'productos';
    public function preciosStocks()
    {
        return $this->hasMany(PrecioStock::class);
    }

    public function detallesCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }
}
