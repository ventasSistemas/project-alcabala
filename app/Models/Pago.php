<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_pago',
        'fecha_pago',
        'fecha_a_pagar',
        'monto',
        'estado',
        'enviado_a_caja',
        'cartilla_id',
        'accesor_id',
    ];

    // Relación con Cartilla
    public function cartilla()
    {
        return $this->belongsTo(Cartilla::class);
    }

    // Relación con Accesor
    public function accesor()
    {
        return $this->belongsTo(Accesor::class);
    }
}