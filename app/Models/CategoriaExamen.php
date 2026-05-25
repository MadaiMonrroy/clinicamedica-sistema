<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaExamen extends Model
{
    use HasFactory;

    protected $table = 'categorias_examenes';

    protected $fillable = [
        'nombre',
        'codigo_prefijo',
        'descripcion',
        'estado',
    ];

    public function examenes(): HasMany
    {
        return $this->hasMany(Examen::class, 'categoria_id');
    }

    public function examenesActivos(): HasMany
    {
        return $this->hasMany(Examen::class, 'categoria_id')
            ->where('estado', 'activo')
            ->orderBy('nombre_examen');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }
}