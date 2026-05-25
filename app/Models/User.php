<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'telefono',
        'rol',
        'especialidad',
        'cargo',
        'activo',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function pacientesRegistrados()
{
    return $this->hasMany(Paciente::class, 'user_id');
}

public function ingresosRecepcionados()
{
    return $this->hasMany(Ingreso::class, 'recepcionista_id');
}

public function enfermeriasRealizados()
{
    return $this->hasMany(Enfermeria::class, 'enfermera_id');
}

public function atencionesMedicas()
{
    return $this->hasMany(Atencion::class, 'medico_id');
}

public function adjuntosLaboratorioSubidos()
{
    return $this->hasMany(AdjuntoLaboratorio::class, 'subido_por');
}
}
