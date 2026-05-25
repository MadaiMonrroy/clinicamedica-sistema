<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interconsulta extends Model
{
    use HasFactory;

    protected $table = 'interconsultas';

    protected $fillable = [
        'atencion_id',
        'area_origen_id',
        'area_destino_id',
        'motivo_interconsulta',
        'observacion',
        'fecha',
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

    public function areaOrigen()
    {
        return $this->belongsTo(Area::class, 'area_origen_id');
    }

    public function areaDestino()
    {
        return $this->belongsTo(Area::class, 'area_destino_id');
    }
}