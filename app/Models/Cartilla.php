<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cartilla extends Model
{
    use HasFactory;

    protected $fillable = [
        'puesto_id',
        'cliente_id',
        'nro',
        'fecha_pagar',
        'cuota',
        'observacion',
        'modo_pago',
        'accesor_cobro',
    ];

    // 🔗 Relaciones
    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // ✅ Formato de fecha legible
    protected $casts = [
        'fecha_pagar' => 'date:Y-m-d',
    ];
}
