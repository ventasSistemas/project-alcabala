<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'user_id',
        'accesor_id',
        'monto_inicial',
        'total_ingresos',
        'total_egresos',
        'saldo_final',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
    ];

    /* Relación con Usuario (si la caja pertenece a un usuario) */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* Relación con Accesor (si la caja pertenece a un accesor) */
    public function accesor()
    {
        return $this->belongsTo(Accesor::class);
    }

    /* Relación con Movimientos */
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
}
