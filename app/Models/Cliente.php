<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'dni',
        'celular',
    ];
    /*

    public function puestos()
    {
        return $this->hasMany(Puesto::class);
    }*/

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }

    public function getNombreCompletoAttribute()
    {
        return strtoupper("{$this->nombres} {$this->apellidos}");
    }

    public function puestos()
    {
        return $this->hasMany(Puesto::class, 'cliente_id');
    }
}