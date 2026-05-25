<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamentos';

    protected $fillable = [
        'cod_medicamento',
        'nombre',
        'presentacion',
        'concentracion',
        'via_administracion',
        'estado',
        'creado_por',
        'actualizado_por',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function detallesReceta()
    {
        return $this->hasMany(DetalleReceta::class);
    }

    public function alergias()
    {
        return $this->hasMany(PacienteAlergia::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    // Nombre completo con presentación y concentración
    public function getNombreCompletoAttribute(): string
    {
        $partes = [$this->nombre];

        if ($this->presentacion)  $partes[] = $this->presentacion;
        if ($this->concentracion) $partes[] = $this->concentracion;

        return implode(' · ', $partes);
    }

    // ¿Tiene todos los campos completos?
    public function getEstaCompletoAttribute(): bool
    {
        return filled($this->presentacion)
            && filled($this->concentracion)
            && filled($this->via_administracion);
    }
}