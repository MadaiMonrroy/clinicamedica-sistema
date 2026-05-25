<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medicamento; // IMPORTANTE: Añade esta línea
class AtencionController extends Controller
{
    /**
     * Lista de atenciones.
     */
    public function index()
    {
        $atenciones = Atencion::with(['ticket.paciente', 'ticket.area'])
            ->latest('fecha_inicio')
            ->paginate(15);

        return view('medico.atenciones.index', compact('atenciones'));
    }

    /**
     * Formulario para iniciar atención.
     * Si ya existe una atención para el ticket → redirige a edit.
     */
    public function create(Ticket $ticket)
    {
        $atencionExistente = Atencion::where('ticket_id', $ticket->id)->latest()->first();

        if ($atencionExistente) {
            return redirect()->route('atenciones.edit', $atencionExistente);
        }

        // Carga de datos para el partial de alergias
        $ticket->load(['paciente.alergias.medicamento', 'paciente.alergias.registradoPor', 'area', 'enfermeria']);
        $medicamentos = Medicamento::where('estado', 'activo')->orderBy('nombre')->get();
        $paciente = $ticket->paciente;

        return view('medico.atenciones.create', compact('ticket', 'paciente', 'medicamentos'));
    }
    /**
     * Guarda atención médica.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'motivo_consulta'   => ['nullable', 'string'],
            'examen_fisico'     => ['nullable', 'string'],
            'diagnostico_texto' => ['nullable', 'string'],
        ]);

        $data['ticket_id']   = $ticket->id;
        $data['medico_id']   = Auth::id();
        $data['estado']      = 'en_curso';
        $data['fecha_inicio'] = now();

        $atencion = Atencion::create($data);

        $ticket->update(['estado' => 'en_turno']);

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('success', 'Atención iniciada correctamente.');
    }

    /**
     * Ver detalle de atención.
     */
    public function show(Atencion $atencion)
    {
        $atencion->load([
            'ticket.paciente',
            'ticket.area',
            'ticket.enfermeria',
            'diagnosticos',
            'recetas',
            'ordenes',
            'interconsultas',
        ]);

        return view('medico.atenciones.show', compact('atencion'));
    }

    /**
     * Editar atención.
     */
    public function edit(Atencion $atencion)
{
    $atencion->load([
        'ticket.paciente.alergias.medicamento',
        'ticket.area',
        'ticket.enfermeria.enfermera',
    ]);
 
    $paciente     = $atencion->ticket->paciente;
    $medicamentos = Medicamento::where('estado', 'activo')
        ->orderBy('nombre')
        ->get();
 
    return view('medico.atenciones.edit', compact('atencion', 'paciente', 'medicamentos'));
}
 

    /**
     * Actualizar atención.
     */
    public function update(Request $request, Atencion $atencion)
    {
        $data = $request->validate([
            'motivo_consulta'   => ['nullable', 'string'],
            'examen_fisico'     => ['nullable', 'string'],
            'diagnostico_texto' => ['nullable', 'string'],
            'estado'            => ['required', 'in:en_curso,finalizada,derivada,observacion'],
        ]);

        $atencion->update($data);

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('success', 'Atención actualizada correctamente.');
    }

    /**
     * Finalizar atención.
     */
    public function finish(Atencion $atencion)
    {
        $atencion->update([
            'estado'   => 'finalizada',
            'fecha_fin' => now(),
        ]);

        $atencion->ticket?->update([
            'estado'       => 'atendido',
            'finalizado_en' => now(),
        ]);

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('success', 'Atención finalizada.');
    }

    /**
     * Pasar atención a observación.
     */
    public function pasarAObservacion(Atencion $atencion)
    {
        $atencion->update(['estado' => 'observacion']);

        return redirect()
            ->route('atenciones.show', $atencion)
            ->with('success', 'Paciente pasado a observación.');
    }
}