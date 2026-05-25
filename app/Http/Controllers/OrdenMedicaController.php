<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\OrdenMedica;
use Illuminate\Http\Request;

class OrdenMedicaController extends Controller
{
    public function index()
    {
        $ordenes = OrdenMedica::with(['atencion.ticket.paciente'])
            ->latest('fecha')
            ->paginate(15);

        return view('medico.ordenes.index', compact('ordenes'));
    }

    public function store(Request $request, Atencion $atencion)
    {
        $data = $request->validate([
            'num_orden' => ['required', 'string', 'max:30'],
            'tipo' => ['required', 'in:laboratorio,imagen,procedimiento,interconsulta'],
            'descripcion' => ['nullable', 'string'],
            'indicaciones' => ['nullable', 'string'],
            'estado' => ['nullable', 'in:pendiente,en_proceso,completada,cancelada'],
        ]);

        $data['atencion_id'] = $atencion->id;
        $data['fecha'] = now();
        $data['estado'] = $data['estado'] ?? 'pendiente';

        $orden = OrdenMedica::create($data);

        return redirect()
            ->route('ordenes-medicas.show', $orden)
            ->with('success', 'Orden médica creada correctamente.');
    }
    public function create(Atencion $atencion)
    {
        $atencion->load(['ticket.paciente', 'ticket.area']);
    
        return view('medico.ordenes.create', compact('atencion'));
    }
    public function show(OrdenMedica $orden)
    {
        $orden->load([
            'atencion.ticket.paciente',
            'examenes',
            'adjuntosLaboratorio',
        ]);

        return view('medico.ordenes.show', compact('orden'));
    }

    public function update(Request $request, OrdenMedica $orden)
    {
        $data = $request->validate([
            'num_orden' => ['required', 'string', 'max:30'],
            'tipo' => ['required', 'in:laboratorio,imagen,procedimiento,interconsulta'],
            'descripcion' => ['nullable', 'string'],
            'indicaciones' => ['nullable', 'string'],
            'estado' => ['required', 'in:pendiente,en_proceso,completada,cancelada'],
        ]);

        $orden->update($data);

        return back()->with('success', 'Orden médica actualizada.');
    }

    public function complete(OrdenMedica $orden)
    {
        $orden->update([
            'estado' => 'completada',
        ]);

        return back()->with('success', 'Orden completada.');
    }

    public function cancel(OrdenMedica $orden)
    {
        $orden->update([
            'estado' => 'cancelada',
        ]);

        return back()->with('success', 'Orden cancelada.');
    }
}