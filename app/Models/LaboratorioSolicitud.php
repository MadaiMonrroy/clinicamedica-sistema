<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaboratorioSolicitud extends Model
{
    protected $table = 'laboratorio_solicitudes';

    protected $fillable = [
        'ingreso_id',
        'examen_id',
        'estado',
        'observacion',
        'muestra_tomada_at',
        'observacion_muestra',
    ];

    protected $casts = [
        'muestra_tomada_at' => 'datetime',
    ];

    // ── Relaciones ─────────────────────────────────────────────────

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeMuestraTomada($query)
    {
        return $query->where('estado', 'muestra_tomada');
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function estadoBadge(): array
    {
        return match ($this->estado) {
            'pendiente'     => ['color' => 'yellow',  'label' => 'Pendiente'],
            'muestra_tomada'=> ['color' => 'blue',    'label' => 'Muestra tomada'],
            'en_proceso'    => ['color' => 'purple',  'label' => 'En proceso'],
            'completado'    => ['color' => 'green',   'label' => 'Completado'],
            default         => ['color' => 'gray',    'label' => ucfirst($this->estado)],
        };
    }
}