<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    use HasFactory;
    protected $table = 'detalle_cotizaciones';
    protected $fillable = ['cotizacion_id', 'producto_id', 'precio_stock_id', 'cantidad', 'subtotal'];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function precioStock()
    {
        return $this->belongsTo(PrecioStock::class);
    }
}
