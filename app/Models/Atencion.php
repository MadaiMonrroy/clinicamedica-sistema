<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    use HasFactory;

    protected $table = 'atenciones';

    protected $fillable = [
        'ticket_id',
        'medico_id',
        'motivo_consulta',
        'examen_fisico',
        'diagnostico_texto',
        'estado',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'atencion_id');
    }

    public function recetas()
    {
        return $this->hasMany(RecetaMedica::class, 'atencion_id');
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenMedica::class, 'atencion_id');
    }

    public function interconsultas()
    {
        return $this->hasMany(Interconsulta::class, 'atencion_id');
    }

    public function evoluciones()
    {
        return $this->hasMany(EvolucionClinica::class, 'atencion_id');
    }
}