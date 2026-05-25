<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Paciente;
use App\Models\LaboratorioSolicitud;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->get('estado');

        $ingresos = Ingreso::with(['paciente'])
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->latest('fecha_ingreso')
            ->paginate(15)
            ->withQueryString();

        return view('recepcion.ingresos.index', compact('ingresos', 'estado'));
    }

   public function create(Request $request)
{
    $paciente = null;
 
    if ($request->filled('paciente_id')) {
        $paciente = Paciente::find($request->integer('paciente_id'));
    }
 
    $numeroPreingreso = $this->generarNumeroPreingreso();
 
    // Cargar categorias con sus examenes activos para el selector de laboratorio
    $categorias = \App\Models\CategoriaExamen::activas()
        ->with(['examenesActivos' => fn($q) => $q->orderBy('nombre_examen')])
        ->orderBy('nombre')
        ->get();
 
    return view('recepcion.ingresos.create', compact('paciente', 'numeroPreingreso', 'categorias'));
}
   public function buscarPaciente(Request $request)
{
    $q = trim($request->get('q', ''));

    if (mb_strlen($q) < 2) {
        return response()->json([]);
    }

    $pacientes = Paciente::query()
        ->where(function ($query) use ($q) {
            $query->whereRaw('unaccent(nombres) ILIKE unaccent(?)', ["%{$q}%"])
                ->orWhereRaw('unaccent(apellido_paterno) ILIKE unaccent(?)', ["%{$q}%"])
                ->orWhereRaw('unaccent(apellido_materno) ILIKE unaccent(?)', ["%{$q}%"])
                ->orWhereRaw('unaccent(ci::text) ILIKE unaccent(?)', ["%{$q}%"]);
        })
        ->limit(10)
        ->get([
            'id',
            'nombres',
            'apellido_paterno',
            'apellido_materno',
            'ci',
            'sexo',
            'fecha_nacimiento',
        ])
        ->map(function ($paciente) {
            return [
                'id' => $paciente->id,
                'nombre_completo' => trim(
                    ($paciente->nombres ?? '') . ' ' .
                    ($paciente->apellido_paterno ?? '') . ' ' .
                    ($paciente->apellido_materno ?? '')
                ),
                'ci' => $paciente->ci,
                'sexo' => $paciente->sexo,
                'edad' => $paciente->fecha_nacimiento
                    ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age
                    : null,
            ];
        })
        ->values();

    return response()->json($pacientes);
}

 public function store(Request $request)
{
    $data = $request->validate([
        'paciente_id'      => ['required', 'exists:pacientes,id'],
        'tipo_ingreso'     => ['required', 'in:enfermeria,laboratorio_directo'],
        'prioridad_inicial'=> ['required', 'in:normal,urgente'],
        'motivo_ingreso'   => ['nullable', 'string'],
        'examenes'         => ['required_if:tipo_ingreso,laboratorio_directo', 'array', 'min:1'],
        'examenes.*'       => ['exists:examenes,id'],
    ]);

    $data['recepcionista_id'] = Auth::id();
    $data['estado'] = $data['tipo_ingreso'] === 'laboratorio_directo'
        ? 'en_area'
        : 'esperando_enfermeria';
    $data['numero_preingreso'] = $this->generarNumeroPreingreso();

    $examenesIds = $data['examenes'] ?? [];
    unset($data['examenes']);

    $ingreso = Ingreso::create($data);

    // Si es laboratorio directo → crear solicitudes + ticket LAB
    if ($ingreso->tipo_ingreso === 'laboratorio_directo' && count($examenesIds) > 0) {

        foreach ($examenesIds as $examenId) {
            LaboratorioSolicitud::create([
                'ingreso_id' => $ingreso->id,
                'examen_id'  => $examenId,
                'estado'     => 'pendiente',
            ]);
        }

        // ── ESTO ES LO QUE FALTABA: crear el ticket en el área LAB ──
        $areaLab = \App\Models\Area::where('tipo', 'laboratorio')->first();

        if ($areaLab) {
            $numeroTicket = $this->generarNumeroTicketLab($areaLab->codigo);

            \App\Models\Ticket::create([
                'ingreso_id'     => $ingreso->id,
                'paciente_id'    => $ingreso->paciente_id,
                'area_id'        => $areaLab->id,
                'numero_ticket'  => $numeroTicket,
                'prioridad_turno'=> $ingreso->prioridad_inicial === 'urgente' ? 'urgente' : 'normal',
                'estado'         => 'en_espera',
            ]);
        }
    }

    return redirect()
        ->route('recepcion.ingresos.show', $ingreso)
        ->with('success', 'Ingreso registrado correctamente.');
}
    public function show(Ingreso $ingreso)
    {
        $ingreso->load([
            'paciente',
            'enfermeria.areaDestino',
            'tickets.area',
        ]);

        return view('recepcion.ingresos.show', compact('ingreso'));
    }

    public function enviarAEnfermeria(Ingreso $ingreso)
    {
        $ingreso->update([
            'tipo_ingreso' => 'enfermeria',
            'estado' => 'esperando_enfermeria',
        ]);

        return redirect()
            ->route('recepcion.ingresos.show', $ingreso)
            ->with('success', 'Ingreso enviado a enfermeria.');
    }

    public function enviarALaboratorio(Request $request, Ingreso $ingreso)
{
    $data = $request->validate([
        'examenes'   => ['required', 'array', 'min:1'],
        'examenes.*' => ['exists:examenes,id'],
    ]);

    // Crear una solicitud por cada examen seleccionado
    foreach ($data['examenes'] as $examenId) {
        LaboratorioSolicitud::create([
            'ingreso_id' => $ingreso->id,
            'examen_id'  => $examenId,
            'estado'     => 'pendiente',
        ]);
    }

    $ingreso->update([
        'tipo_ingreso' => 'laboratorio_directo',
        'estado'       => 'en_area',
    ]);

    return redirect()
        ->route('recepcion.ingresos.show', $ingreso)
        ->with('success', 'Ingreso enviado a laboratorio con ' . count($data['examenes']) . ' exámenes.');
}
    public function cancel(Ingreso $ingreso)
    {
        $ingreso->update([
            'estado' => 'cancelado',
        ]);

        return redirect()
            ->route('recepcion.ingresos.index')
            ->with('success', 'Ingreso cancelado correctamente.');
    }
    private function generarNumeroPreingreso()
{
    $ultimo = Ingreso::query()
        ->whereNotNull('numero_preingreso')
        ->where('numero_preingreso', 'like', 'PRE-%')
        ->select('numero_preingreso')
        ->get()
        ->map(function ($ingreso) {
            return (int) str_replace('PRE-', '', $ingreso->numero_preingreso);
        })
        ->max();

    $siguiente = $ultimo ? $ultimo + 1 : 1;

    return 'PRE-' . $siguiente;
}
private function generarNumeroTicketLab(string $codigoArea): string
{
    $ultimo = \App\Models\Ticket::whereHas('area', fn($q) => $q->where('tipo', 'laboratorio'))
        ->select('numero_ticket')
        ->get()
        ->map(function ($t) use ($codigoArea) {
            $parte = str_replace($codigoArea . '-', '', $t->numero_ticket);
            return is_numeric($parte) ? (int) $parte : 0;
        })
        ->max();

    $siguiente = $ultimo ? $ultimo + 1 : 1;

    return $codigoArea . '-' . str_pad($siguiente, 3, '0', STR_PAD_LEFT);
}
}