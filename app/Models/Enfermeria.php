<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enfermeria extends Model
{
    use HasFactory;

    protected $table = 'enfermeria';

    protected $fillable = [
        'ingreso_id',
        'enfermera_id',
        'area_destino_id',
        'temperatura',
        'presion_arterial',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'saturacion_oxigeno',
        'peso',
        'talla',
        'observacion',
        'prioridad_clinica',
        'fecha_enfermeria',
    ];

    protected $casts = [
        'temperatura' => 'decimal:2',
        'saturacion_oxigeno' => 'decimal:2',
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
        'fecha_enfermeria' => 'datetime',
        'created_at' => 'datetime',
        
        'updated_at' => 'datetime',
    ];

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class, 'ingreso_id');
    }

    public function enfermera()
    {
        return $this->belongsTo(User::class, 'enfermera_id');
    }

    public function areaDestino()
    {
        return $this->belongsTo(Area::class, 'area_destino_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'enfermeria_id');
    }
}