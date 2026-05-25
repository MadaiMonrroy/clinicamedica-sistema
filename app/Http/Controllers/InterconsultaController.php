<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use App\Models\Interconsulta;
use Illuminate\Http\Request;

class InterconsultaController extends Controller
{
    public function index()
    {
        $interconsultas = Interconsulta::with(['atencion.ticket.paciente', 'areaOrigen', 'areaDestino'])
            ->latest('fecha')
            ->paginate(15);

        return view('medico.interconsultas.index', compact('interconsultas'));
    }

    public function store(Request $request, Atencion $atencion)
    {
        $data = $request->validate([
            'area_origen_id' => ['required', 'exists:areas,id'],
            'area_destino_id' => ['required', 'exists:areas,id', 'different:area_origen_id'],
            'motivo_interconsulta' => ['required', 'string'],
            'observacion' => ['nullable', 'string'],
        ]);

        $data['atencion_id'] = $atencion->id;
        $data['fecha'] = now();
        $data['estado'] = 'pendiente';

        $interconsulta = Interconsulta::create($data);

        $atencion->update([
            'estado' => 'derivada',
        ]);

        return redirect()
            ->route('interconsultas.show', $interconsulta)
            ->with('success', 'Derivación registrada correctamente.');
    }

    public function show(Interconsulta $interconsulta)
    {
        $interconsulta->load([
            'atencion.ticket.paciente',
            'areaOrigen',
            'areaDestino',
        ]);

        return view('medico.interconsultas.show', compact('interconsulta'));
    }

    public function accept(Interconsulta $interconsulta)
    {
        $interconsulta->update([
            'estado' => 'aceptada',
        ]);

        return back()->with('success', 'Derivación aceptada.');
    }
    public function create(Atencion $atencion)
    {
        $atencion->load(['ticket.paciente', 'ticket.area']);
        $areas = \App\Models\Area::where('estado', 'activo')->orderBy('nombre')->get();
    
        return view('medico.interconsultas.create', compact('atencion', 'areas'));
    }
    public function complete(Interconsulta $interconsulta)
    {
        $interconsulta->update([
            'estado' => 'completada',
        ]);

        return back()->with('success', 'Derivación completada.');
    }

    public function cancel(Interconsulta $interconsulta)
    {
        $interconsulta->update([
            'estado' => 'cancelada',
        ]);

        return back()->with('success', 'Derivación cancelada.');
    }
}