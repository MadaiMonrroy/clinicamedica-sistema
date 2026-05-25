<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PacienteAlergia;
class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'ci',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'email',
        'direccion',
        'fecha_nacimiento',
        'sexo',
        'estado',
        'observacion',
        'user_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class, 'paciente_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'paciente_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors útiles
    |--------------------------------------------------------------------------
    */

    public function getNombreCompletoAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->nombres,
            $this->apellido_paterno,
            $this->apellido_materno,
        ])));
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }
    // Todas las alergias del paciente
public function alergias()
{
    return $this->hasMany(PacienteAlergia::class);
}
 
// Solo alergias a medicamentos (útil para validar recetas)
public function alergiasAMedicamentos()
{
    return $this->hasMany(PacienteAlergia::class)
        ->where('tipo', 'medicamento')
        ->whereNotNull('medicamento_id');
}
 
}