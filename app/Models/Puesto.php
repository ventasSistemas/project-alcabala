<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    use HasFactory;

    protected $fillable = [
    'categoria_id',
    'numero_puesto',
    'disponible',
    'cliente_id',
    'imagen_puesto',
    'servicios',
    'observaciones',
    'fecha_inicio',
    'fecha_fin',
    'hora_apertura',
    'hora_cierre',
    'primer_pago_fecha',
    'primer_pago_monto',
    'modo_pago',
    'accesor_cobro',
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'servicios' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_apertura' => 'datetime:H:i',
        'hora_cierre' => 'datetime:H:i',
        'primer_pago_fecha' => 'date',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaEstablecimiento::class, 'categoria_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'puesto_id');
    }

    public function getEstadoAttribute()
    {
        return $this->disponible ? 'Disponible' : 'Ocupado';
    }

    protected static function booted()
    {
        static::saving(function ($puesto) {
            $puesto->disponible = $puesto->cliente_id === null;
        });
    }

    public function cartillas()
    {
        return $this->hasMany(Cartilla::class, 'puesto_id');
    }
}