<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenMedica extends Model
{
    use HasFactory;

    protected $table = 'ordenes_medicas';

    protected $fillable = [
        'atencion_id',
        'num_orden',
        'fecha',
        'tipo',
        'descripcion',
        'indicaciones',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'datetime',
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

    public function examenes()
    {
        return $this->belongsToMany(
            Examen::class,
            'orden_medica_examen',
            'orden_medica_id',
            'examen_id'
        );
    }

    public function adjuntosLaboratorio()
    {
        return $this->hasMany(AdjuntoLaboratorio::class, 'orden_medica_id');
    }
}