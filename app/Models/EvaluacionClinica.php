<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvolucionClinica extends Model
{
    use HasFactory;

    protected $table = 'evoluciones_clinicas';

    protected $fillable = [
        'atencion_id',
        'descripcion_evolucion',
        'observacion',
        'estado_paciente',
        'fecha_evolucion',
    ];

    protected $casts = [
        'fecha_evolucion' => 'datetime',
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
}