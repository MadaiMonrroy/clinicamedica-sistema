<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Ingreso;
use App\Models\Ticket;
use App\Models\Enfermeria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class  EnfermeriaController extends Controller
{
    /**
     * Cola principal de enfermeria.
     * Prioriza urgentes y luego por fecha.
     */
    public function index()
    {
        $ingresos = Ingreso::with('paciente')
            ->where('estado', 'esperando_enfermeria')
            ->orderByRaw("CASE WHEN prioridad_inicial = 'urgente' THEN 0 ELSE 1 END")
            ->orderBy('fecha_ingreso')
            ->paginate(15);

        return view('medico.enfermeria.index', compact('ingresos'));
    }

    /**
     * Alias para pendientes.
     */
    public function pending()
    {
        return $this->index();
    }

    /**
     * Formulario para atender en enfermeria.
     */
    public function create(Ingreso $ingreso)
    {
        $areas = Area::where('estado', 'activo')
            ->whereIn('tipo', ['consulta', 'emergencia'])
            ->orderBy('nombre')
            ->get();

        return view('medico.enfermeria.create', compact('ingreso', 'areas'));
    }

    /**
     * Guarda datos de enfermeria.
     */
    public function store(Request $request, Ingreso $ingreso)
{
    $data = $request->validate([
        'area_destino_id' => ['required', 'exists:areas,id'],
        'temperatura' => ['nullable', 'numeric'],
        'presion_arterial' => ['nullable', 'string', 'max:20'],
        'frecuencia_cardiaca' => ['nullable', 'integer'],
        'frecuencia_respiratoria' => ['nullable', 'integer'],
        'saturacion_oxigeno' => ['nullable', 'numeric'],
        'peso' => ['nullable', 'numeric'],
        'talla' => ['nullable', 'numeric'],
        'observacion' => ['nullable', 'string'],
        'prioridad_clinica' => ['required', 'in:baja,media,alta,critica'],
    ]);

    $data['ingreso_id'] = $ingreso->id;
    $data['enfermera_id'] = Auth::id();

    $ticket = DB::transaction(function () use ($data, $ingreso) {
        $enfermeria =Enfermeria::create($data);
        $area = $enfermeria->areaDestino;

        $prioridadTurno = match ($enfermeria->prioridad_clinica) {
            'critica' => 'critico',
            'alta' => 'urgente',
            default => 'normal',
        };

        $ticket = Ticket::create([
            'ingreso_id' => $ingreso->id,
            'paciente_id' => $ingreso->paciente_id,
            'area_id' => $area->id,
            'enfermeria_id' => $enfermeria->id,
            'numero_ticket' => $this->generarNumeroTicket($area->codigo),
            'prioridad_turno' => $prioridadTurno,
            'estado' => 'en_espera',
        ]);

        $ingreso->update([
            'estado' => 'en_area',
        ]);

        return $ticket;
    });

    return redirect()
        ->route('tickets.show', $ticket)
        ->with('success', 'Enfermeria registrado y ticket generado correctamente.');
}

public function generarTicket(Ingreso $ingreso)
{
    return redirect()
        ->route('enfermeria.create', $ingreso)
        ->with('info', 'El ticket se genera al guardar el enfermeria.');
}

    /**
     * Generador simple de ticket por área.
     * Luego puedes refinarlo por fecha/contador diario.
     */
    protected function generarNumeroTicket(string $codigoArea): string
    {
        $numero = str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
        return "{$codigoArea}-{$numero}";
    }
}