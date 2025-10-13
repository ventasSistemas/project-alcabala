<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaEstablecimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'imagen_lugar',
        'longitud_actual',
        'latitud_actual',
        'longitud_destino',
        'latitud_destino',
        'hora_apertura',
        'hora_cierre',
        'pago_puesto',
        'pago_inscripcion_anual',
    ];

    /**
     * Relación muchos a muchos con Accesor (personales asignados)
     */
    public function accesores()
    {
        return $this->belongsToMany(Accesor::class, 'accesor_categoria');
    }

    /**
     * Relación con Puestos (si los usas)
     */
    public function puestos()
    {
        return $this->hasMany(Puesto::class, 'categoria_id');
    }
}
