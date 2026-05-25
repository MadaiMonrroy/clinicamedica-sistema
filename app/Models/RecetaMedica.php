<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaMedica extends Model
{
    use HasFactory;

    protected $table = 'recetas_medicas';

    protected $fillable = [
        'atencion_id',
        'numero_receta',
        'fecha_receta',
        'indicacion_general',
        'estado',
    ];

    protected $casts = [
        'fecha_receta' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function atencion()
    {
        return $this->belongsTo(Atencion::class, 'atencion_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReceta::class, 'receta_id');
    }
}