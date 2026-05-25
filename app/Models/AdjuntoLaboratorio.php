<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdjuntoLaboratorio extends Model
{
    protected $table = 'adjuntos_laboratorio';

    protected $fillable = [
        'orden_medica_id',
        'ticket_id',
        'examen_id',
        'solicitud_id',
        'subido_por',
        'nombre_archivo',
        'ruta_archivo',
        'observacion',
        'fecha_subida',
        'estado',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    // ── Relaciones ─────────────────────────────────────────────────

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function ordenMedica(): BelongsTo
    {
        return $this->belongsTo(OrdenMedica::class);
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(LaboratorioSolicitud::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}