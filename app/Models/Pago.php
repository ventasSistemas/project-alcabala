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
        'cartilla_id'
    ];

    public function cartilla()
    {
        return $this->belongsTo(Cartilla::class);
    }
}
