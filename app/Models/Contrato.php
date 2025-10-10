<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'puesto_id',
        'fecha_inicio',
        'fecha_fin',
        'frecuencia_pago',
        'monto',
        'renovable',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}