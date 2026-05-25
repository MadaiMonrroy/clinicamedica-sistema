<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['nombre', 'codigo', 'tipo', 'estado'];

    /**
     * Sugerencias de tipo — no son restrictivas.
     * Puedes agregar aquí los nuevos tipos que vayas creando
     * para que aparezcan como sugerencias en el formulario.
     */
    const TIPOS_SUGERIDOS = [
        'enfermeria',
        'consulta',
        'emergencia',
        'laboratorio',
        'imagen',
        'procedimiento',
    ];

    const ESTADOS = ['activo', 'inactivo'];

    // ── Relaciones ──────────────────────────────────────────

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function enfermeria(): HasMany
    {
        return $this->hasMany(Enfermeria::class, 'area_destino_id');
    }

    public function derivacionesOrigen(): HasMany
    {
        return $this->hasMany(Interconsulta::class, 'area_origen_id');
    }

    public function derivacionesDestino(): HasMany
    {
        return $this->hasMany(Interconsulta::class, 'area_destino_id');
    }

    // ── Helpers ─────────────────────────────────────────────

    public function ticketsActivosHoy(): int
    {
        return $this->tickets()
            ->whereIn('estado', ['en_espera', 'en_turno'])
            ->whereDate('created_at', today())
            ->count();
    }

    public function tieneDependencias(): bool
    {
        return $this->tickets()->exists()
            || $this->enfermeria()->exists()
            || $this->derivacionesOrigen()->exists()
            || $this->derivacionesDestino()->exists();
    }
public function totalTickets(): int
{
    return $this->tickets()->count();
}
    // ── Scopes ──────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }
}