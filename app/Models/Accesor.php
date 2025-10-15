<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accesor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'direccion',
        'celular',
        'dni',
        'user_id',
    ];

    public function puestos()
    {
        return $this->belongsToMany(Puesto::class, 'accesor_puesto');
    }

    // Relaciones
    public function categorias()
    {
        return $this->hasMany(CategoriaEstablecimiento::class, 'accesor_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'accesor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
