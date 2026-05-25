<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteAlergia extends Model
{
    protected $table = 'paciente_alergias';

    protected $fillable = [
        'paciente_id',
        'tipo',
        'descripcion',
        'medicamento_id',
        'severidad',
        'reaccion',
        'registrado_por',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // ── Helpers ───────────────────────────────────────────────

    // Color del badge según severidad
    public function severidadClase(): string
    {
        return match($this->severidad) {
            'grave'    => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'moderada' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            'leve'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
            default    => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        };
    }
}