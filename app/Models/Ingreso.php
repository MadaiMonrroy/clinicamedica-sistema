<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LaboratorioSolicitud;

use App\Models\Paciente;
use App\Models\User;
use App\Models\Enfermeria;
use App\Models\Ticket;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
        'paciente_id',
        'recepcionista_id',
        'tipo_ingreso',
        'prioridad_inicial',
        'motivo_ingreso',
        'estado',
        'numero_preingreso',
        'fecha_ingreso',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function recepcionista()
    {
        return $this->belongsTo(User::class, 'recepcionista_id');
    }

    public function enfermeria()
    {
        return $this->hasMany(Enfermeria::class, 'ingreso_id');
    }

    public function ultimoEnfermeria()
    {
        return $this->hasOne(Enfermeria::class, 'ingreso_id')->latestOfMany();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ingreso_id');
    }
    public function laboratorioSolicitudes()
{
    return $this->hasMany(LaboratorioSolicitud::class, 'ingreso_id');
}

public function esDirecctoLaboratorio(): bool
{
    return $this->tipo_ingreso === 'laboratorio_directo';
}
public function solicitudesLab(): HasMany
{
    return $this->hasMany(LaboratorioSolicitud::class, 'ingreso_id');
}
}