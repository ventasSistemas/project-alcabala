<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id',
        'pago_id',
        'tipo',
        'descripcion',
        'monto',
    ];

    /* Relación con Caja */
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    /* Relación con Pago (si existe) */
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}