<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'ingreso_id',
        'paciente_id',
        'area_id',
        'enfermeria_id',
        'numero_ticket',
        'prioridad_turno',
        'estado',
        'llamado_en',
        'finalizado_en',
        'observacion',
    ];

    protected $casts = [
        'llamado_en' => 'datetime',
        'finalizado_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class, 'ingreso_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function enfermeria()
    {
        return $this->belongsTo(Enfermeria::class, 'enfermeria_id');
    }
    public function adjuntosLab(): HasMany
{
    return $this->hasMany(\App\Models\AdjuntoLaboratorio::class, 'ticket_id');
}
// App/Models/Ticket.php
public function atenciones()
{
    return $this->hasMany(Atencion::class);
}
}