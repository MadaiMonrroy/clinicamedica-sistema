<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoriaExamen;
class Examen extends Model
{
    use HasFactory;

    protected $table = 'examenes';

    protected $fillable = [
        'categoria_id',
        'cod_examen',
        'nombre_examen',
        'tipo_examen',
        'descripcion',
        'costo_ref',
        'estado',
    ];

    protected $casts = [
        'costo_ref'  => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relaciones ──

    // Renombramos a categoriaExamen para no chocar con nada
    public function categoriaExamen()
    {
        return $this->belongsTo(CategoriaExamen::class, 'categoria_id');
    }

    public function ordenesMedicas()
    {
        return $this->belongsToMany(
            OrdenMedica::class,
            'orden_medica_examen',
            'examen_id',
            'orden_medica_id'
        );
    }

    public function adjuntosLaboratorio()
    {
        return $this->hasMany(AdjuntoLaboratorio::class, 'examen_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(LaboratorioSolicitud::class, 'examen_id');
    }

    // ── Scopes ──

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
    
}