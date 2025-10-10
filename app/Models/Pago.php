<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrato_id',
        'accesor_id',
        'fecha_pago',
        'fecha_a_pagar',
        'monto',
        'estado',
        'observacion',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function accesor()
    {
        return $this->belongsTo(Accesor::class);
    }

    // Accessor ejemplo para estado con color
    public function getEstadoBadgeAttribute()
    {
        return $this->estado === 'PAGADO'
            ? '<span class="badge bg-success">Pagado</span>'
            : '<span class="badge bg-warning text-dark">Pendiente</span>';
    }
}
